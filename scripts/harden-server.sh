#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "  YourMindAI - Server Security Hardening"
echo "=========================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

echo -e "${GREEN}[1/8]${NC} Configuring fail2ban..."

# Create fail2ban jail for nginx
cat > /etc/fail2ban/jail.d/nginx.conf << 'EOF'
[nginx-limit-req]
enabled = true
filter = nginx-limit-req
logpath = /var/log/nginx/*error.log
maxretry = 10
findtime = 600
bantime = 3600

[nginx-botsearch]
enabled = true
filter = nginx-botsearch
logpath = /var/log/nginx/*access.log
maxretry = 2
findtime = 600
bantime = 86400
EOF

# Create nginx-limit-req filter
cat > /etc/fail2ban/filter.d/nginx-limit-req.conf << 'EOF'
[Definition]
failregex = limiting requests, excess:.* by zone.*client: <HOST>
ignoreregex =
EOF

# Create nginx-botsearch filter
cat > /etc/fail2ban/filter.d/nginx-botsearch.conf << 'EOF'
[Definition]
failregex = ^<HOST> -.*"(GET|POST|HEAD).*HTTP.*" 404.*$
ignoreregex =
EOF

# Create fail2ban jail for SSH (if not exists)
if [ ! -f "/etc/fail2ban/jail.d/sshd.conf" ]; then
    cat > /etc/fail2ban/jail.d/sshd.conf << 'EOF'
[sshd]
enabled = true
port = ssh
logpath = %(sshd_log)s
maxretry = 5
findtime = 600
bantime = 3600
EOF
fi

systemctl restart fail2ban
systemctl enable fail2ban

echo -e "${GREEN}[2/8]${NC} Configuring firewall rules..."

# Configure UFW
ufw --force enable || true
ufw default deny incoming
ufw default allow outgoing

# Allow essential services
ufw allow 22/tcp comment 'SSH'
ufw allow 80/tcp comment 'HTTP'
ufw allow 443/tcp comment 'HTTPS'

# Reload firewall
ufw --force reload

echo -e "${GREEN}[3/8]${NC} Configuring SSH security..."

# Backup SSH config
cp /etc/ssh/sshd_config /etc/ssh/sshd_config.backup.$(date +%Y%m%d_%H%M%S)

# Secure SSH configuration
sed -i 's/#PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config 2>/dev/null || true
sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config 2>/dev/null || true
sed -i 's/#PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config 2>/dev/null || true
sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/' /etc/ssh/sshd_config 2>/dev/null || true
sed -i 's/#PubkeyAuthentication yes/PubkeyAuthentication yes/' /etc/ssh/sshd_config 2>/dev/null || true

# Add additional security settings
if ! grep -q "MaxAuthTries" /etc/ssh/sshd_config; then
    echo "MaxAuthTries 3" >> /etc/ssh/sshd_config
fi

if ! grep -q "ClientAliveInterval" /etc/ssh/sshd_config; then
    echo "ClientAliveInterval 300" >> /etc/ssh/sshd_config
    echo "ClientAliveCountMax 2" >> /etc/ssh/sshd_config
fi

# Test SSH config before applying
if sshd -t; then
    systemctl restart sshd
    echo -e "${GREEN}SSH configuration updated${NC}"
else
    echo -e "${RED}SSH configuration test failed. Restoring backup...${NC}"
    cp /etc/ssh/sshd_config.backup.* /etc/ssh/sshd_config
    exit 1
fi

echo -e "${YELLOW}Note: Make sure you have SSH keys set up before disabling password authentication!${NC}"

echo -e "${GREEN}[4/8]${NC} Setting up automatic security updates..."

# Configure unattended-upgrades
cat > /etc/apt/apt.conf.d/50unattended-upgrades << 'EOF'
Unattended-Upgrade::Allowed-Origins {
    "${distro_id}:${distro_codename}-security";
    "${distro_id}ESMApps:${distro_codename}-apps-security";
    "${distro_id}ESM:${distro_codename}-infra-security";
};
Unattended-Upgrade::AutoFixInterruptedDpkg "true";
Unattended-Upgrade::MinimalSteps "true";
Unattended-Upgrade::Remove-Unused-Kernel-Packages "true";
Unattended-Upgrade::Remove-Unused-Dependencies "true";
Unattended-Upgrade::Automatic-Reboot "false";
Unattended-Upgrade::Mail "root";
EOF

echo 'APT::Periodic::Update-Package-Lists "1";
APT::Periodic::Unattended-Upgrade "1";
APT::Periodic::Download-Upgradeable-Packages "1";
APT::Periodic::AutocleanInterval "7";' > /etc/apt/apt.conf.d/20auto-upgrades

systemctl enable unattended-upgrades
systemctl start unattended-upgrades

echo -e "${GREEN}[5/8]${NC} Setting up log rotation..."

# Create logrotate config for Laravel
cat > /etc/logrotate.d/laravel << 'EOF'
/path/to/yourmindai-laravel/storage/logs/*.log {
    daily
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        docker-compose -f /path/to/yourmindai-laravel/docker-compose.yml exec -T php-fpm php artisan log:clear 2>/dev/null || true
    endscript
}
EOF

# Update path in logrotate config
sed -i "s|/path/to/yourmindai-laravel|$(pwd)|g" /etc/logrotate.d/laravel

# Create logrotate config for nginx
cat > /etc/logrotate.d/nginx-docker << 'EOF'
/var/lib/docker/containers/*/*-json.log {
    daily
    rotate 7
    compress
    delaycompress
    notifempty
    create 0640 root root
    sharedscripts
    postrotate
        docker kill -s USR1 yourmindai_nginx 2>/dev/null || true
    endscript
}
EOF

echo -e "${GREEN}[6/8]${NC} Configuring PHP security settings..."

# PHP security settings are already configured in Dockerfile
# This section is for reference
echo "PHP security settings are configured in the Dockerfile"
echo "  - expose_php = Off"
echo "  - Dangerous functions disabled"
echo "  - allow_url_fopen = Off"

echo -e "${GREEN}[7/8]${NC} Setting up system limits..."

# Increase file descriptor limits
cat >> /etc/security/limits.conf << 'EOF'
* soft nofile 65535
* hard nofile 65535
www-data soft nofile 65535
www-data hard nofile 65535
EOF

# Configure sysctl for security
cat >> /etc/sysctl.d/99-security.conf << 'EOF'
# IP Spoofing protection
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1

# Ignore ICMP redirects
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.secure_redirects = 0
net.ipv4.conf.default.secure_redirects = 0
net.ipv6.conf.all.accept_redirects = 0
net.ipv6.conf.default.accept_redirects = 0

# Ignore send redirects
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0

# Disable source packet routing
net.ipv4.conf.all.accept_source_route = 0
net.ipv4.conf.default.accept_source_route = 0
net.ipv6.conf.all.accept_source_route = 0
net.ipv6.conf.default.accept_source_route = 0

# Log Martians
net.ipv4.conf.all.log_martians = 1
net.ipv4.conf.default.log_martians = 1

# Ignore ICMP ping requests
net.ipv4.icmp_echo_ignore_all = 0

# Ignore Directed pings
net.ipv4.icmp_echo_ignore_broadcasts = 1

# Enable SYN flood protection
net.ipv4.tcp_syncookies = 1
net.ipv4.tcp_max_syn_backlog = 2048
net.ipv4.tcp_synack_retries = 2
net.ipv4.tcp_syn_retries = 5

# Enable IP forwarding (required for Docker)
net.ipv4.ip_forward = 1
EOF

sysctl -p /etc/sysctl.d/99-security.conf

echo -e "${GREEN}[8/8]${NC} Finalizing security configuration..."

# Set proper permissions on sensitive files
chmod 600 /etc/ssh/sshd_config 2>/dev/null || true
chmod 644 /etc/ssh/sshd_config.pub 2>/dev/null || true

# Disable unnecessary services (optional)
systemctl disable bluetooth 2>/dev/null || true
systemctl stop bluetooth 2>/dev/null || true

echo ""
echo "=========================================="
echo -e "${GREEN}Server Security Hardening Complete!${NC}"
echo "=========================================="
echo ""
echo "Security measures applied:"
echo "  ✓ fail2ban configured for nginx and SSH"
echo "  ✓ Firewall (UFW) configured"
echo "  ✓ SSH security enhanced"
echo "  ✓ Automatic security updates enabled"
echo "  ✓ Log rotation configured"
echo "  ✓ System limits increased"
echo "  ✓ Network security hardened"
echo ""
echo -e "${YELLOW}Important:${NC}"
echo "  1. Make sure you have SSH keys set up before disabling password authentication"
echo "  2. Review fail2ban configuration: /etc/fail2ban/jail.d/"
echo "  3. Test SSH access before logging out"
echo "  4. Monitor fail2ban logs: fail2ban-client status"
echo ""
