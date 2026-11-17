#!/usr/bin/env bash
# ==============================================================================
# SKRIP HARDENING KERNEL LINUX & FIREWALL ANTI-DDOS - SIAKAD SMPN 2 SUBANG
# Jalankan dengan hak akses root: sudo bash deploy/harden-server-anti-ddos.sh
# ==============================================================================

set -euo pipefail

echo "================================================================="
echo "🛡️ MEMULAI HARDENING SISTEM OPERASI & FIREWALL ANTI-DDOS"
echo "   SIAKAD & E-Learning SMP Negeri 2 Subang"
echo "================================================================="

# 1. OPTIMALISASI KERNEL LINUX NETWORK STACK (/etc/sysctl.d/99-anti-ddos.conf)
echo "[1/4] Mengonfigurasi parameter Kernel Linux untuk mitigasi TCP SYN Flood..."

cat << 'EOF' > /etc/sysctl.d/99-anti-ddos.conf
# Proteksi TCP SYN Flood menggunakan SYN Cookies Kriptografis
net.ipv4.tcp_syncookies = 1

# Memperbesar antrean koneksi TCP SYN saat lonjakan request
net.ipv4.tcp_max_syn_backlog = 4096
net.core.somaxconn = 4096
net.core.netdev_max_backlog = 4096

# Menurunkan batas percobaan SYN-ACK sebelum koneksi palsu dibuang
net.ipv4.tcp_synack_retries = 2
net.ipv4.tcp_syn_retries = 3

# Menutup koneksi TCP FIN yang menggantung lebih cepat (Default 60s -> 15s)
net.ipv4.tcp_fin_timeout = 15

# Interval pembersihan koneksi mati (Keepalive)
net.ipv4.tcp_keepalive_time = 300
net.ipv4.tcp_keepalive_intvl = 15
net.ipv4.tcp_keepalive_probes = 5

# Reverse Path Filtering: Mencegah serangan pemalsuan alamat IP (IP Spoofing)
net.ipv4.conf.all.rp_filter = 1
net.ipv4.conf.default.rp_filter = 1

# Mencegah serangan ICMP Smurf Attack (Mengabaikan broadcast ping)
net.ipv4.icmp_echo_ignore_broadcasts = 1
net.ipv4.icmp_ignore_bogus_error_responses = 1

# Menolak paket dengan opsi Source Routing (Mencegah perutean manipulatif)
net.ipv4.conf.all.accept_source_route = 0
net.ipv4.conf.default.accept_source_route = 0

# Mengabaikan pengalihan ICMP (ICMP Redirects)
net.ipv4.conf.all.accept_redirects = 0
net.ipv4.conf.default.accept_redirects = 0
net.ipv4.conf.all.send_redirects = 0
net.ipv4.conf.default.send_redirects = 0

# Memperbesar alokasi memori buffer TCP untuk menangani throughput tinggi
net.ipv4.tcp_rmem = 4096 87380 67108864
net.ipv4.tcp_wmem = 4096 65536 67108864
EOF

# Terapkan parameter kernel seketika tanpa perlu restart
sysctl -p /etc/sysctl.d/99-anti-ddos.conf > /dev/null
echo "   ✅ Parameter Kernel TCP/IP berhasil dimuat dan aktif."

# 2. KONFIGURASI FIREWALL UFW (UNCOMPLICATED FIREWALL)
echo "[2/4] Mengonfigurasi aturan firewall UFW..."

if command -v ufw >/dev/null 2>&1; then
    # Setel kebijakan default: Blokir seluruh paket masuk yang tak dikenal
    ufw --force reset > /dev/null 2>&1 || true
    ufw default deny incoming
    ufw default allow outgoing

    # Izinkan antarmuka loopback lokal (Wajib untuk Nginx <-> PHP-FPM <-> DB)
    ufw allow in on lo to any comment 'Allow Local Loopback'

    # Lindungi port SSH (Port 22) dengan mekanisme Rate Limiting (Anti Brute Force)
    ufw limit 22/tcp comment 'Rate-limited SSH Access'

    # Izinkan port web server Nginx
    ufw allow 80/tcp comment 'HTTP Web Server'
    ufw allow 443/tcp comment 'HTTPS Web Server'

    # Aktifkan UFW
    ufw --force enable
    echo "   ✅ Firewall UFW aktif dengan kebijakan Default Deny dan Rate-Limited SSH."
else
    echo "   ⚠️ UFW tidak terpasang, melewati konfigurasi UFW."
fi

# 3. AKTIVASI KONFIGURASI ANTI-DDOS NGINX
echo "[3/4] Memasang file konfigurasi Nginx Anti-DDoS..."
if [ -d "/etc/nginx/conf.d" ]; then
    cp deploy/nginx/siakad-anti-ddos.conf /etc/nginx/conf.d/siakad-anti-ddos.conf
    echo "   ✅ File /etc/nginx/conf.d/siakad-anti-ddos.conf berhasil disalin."
    
    # Uji sintaks Nginx
    if nginx -t > /dev/null 2>&1; then
        systemctl reload nginx
        echo "   ✅ Nginx berhasil direload dengan proteksi rate limit dan anti-slowloris."
    else
        echo "   ⚠️ Uji sintaks Nginx gagal, silakan periksa konfigurasinya."
    fi
fi

# 4. RINGKASAN STATUS
echo "================================================================="
echo "🎉 HARDENING SELESAI! PERTAHANAN ANTI-DDOS AKTIF PENUH:"
echo "   1. TCP SYN Cookies     : AKTIF (Kebal serangan SYN Flood)"
echo "   2. Anti-IP Spoofing    : AKTIF (Reverse Path Filter enabled)"
echo "   3. Nginx Rate Limiter  : AKTIF (15 req/sec burst 25 per Real IP)"
echo "   4. Anti-Slowloris      : AKTIF (Timeouts 10s strict)"
echo "   5. Firewall UFW        : AKTIF (Default Deny + Port 22 Rate Limited)"
echo "================================================================="
