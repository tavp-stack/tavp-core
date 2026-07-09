#!/usr/bin/env bash
#
# install-phalcon.sh — Install Phalcon 5.16 on any Debian/Ubuntu-based
# system (Lando container, VPS, Docker) with a single command.
#
# Usage:
#   sudo bash install-phalcon.sh            # auto-detect PHP version
#   sudo bash install-phalcon.sh 8.3        # pin a PHP version
#
# The script is idempotent: if Phalcon is already loaded it exits early.
#
set -euo pipefail

PHALCON_VERSION="${2:-5.16.0}"
PHP_VERSION="${1:-$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;' 2>/dev/null || echo 8.3)}"

echo "==> TAVP Phalcon installer"
echo "    Phalcon : ${PHALCON_VERSION}"
echo "    PHP     : ${PHP_VERSION}"

# --- 1. Already installed? ---------------------------------------------
if php -m 2>/dev/null | grep -qi '^phalcon$'; then
    echo "==> Phalcon is already installed. Nothing to do."
    exit 0
fi

# --- 2. Root / sudo check ----------------------------------------------
if [ "$(id -u)" -ne 0 ]; then
    echo "ERROR: this script needs root. Run with sudo or inside a root container." >&2
    exit 1
fi

# --- 3. OS family -------------------------------------------------------
if [ -f /etc/os-release ]; then
    # shellcheck disable=SC1091
    . /etc/os-release
fi

case "${ID:-unknown}" in
    ubuntu|debian|raspbian)
        echo "==> Detected Debian/Ubuntu family."
        ;;
    *)
        echo "WARNING: untested OS '${ID:-unknown}'. Proceeding anyway." >&2
        ;;
esac

# --- 4. Install build dependencies -------------------------------------
echo "==> Installing build dependencies..."
export DEBIAN_FRONTEND=noninteractive
apt-get update -y
apt-get install -y \
    wget git curl build-essential autoconf pkg-config \
    re2c libpcre3-dev libpcre2-dev zlib1g-dev \
    "php${PHP_VERSION}-dev" "php${PHP_VERSION}-xml" "php-pear"

# Make sure phpize / php-config for the target version are on PATH.
PHPIZE="phpize${PHP_VERSION}"
PHPCONFIG="php-config${PHP_VERSION}"
command -v "${PHPIZE}" >/dev/null 2>&1 || PHPIZE="phpize"
command -v "${PHPCONFIG}" >/dev/null 2>&1 || PHPCONFIG="php-config"

# --- 5. Download Phalcon source (PECL tarball) -------------------------
echo "==> Downloading Phalcon ${PHALCON_VERSION} source..."
WORKDIR="$(mktemp -d)"
cd "${WORKDIR}"
wget -q "https://github.com/phalcon/cphalcon/releases/download/v${PHALCON_VERSION}/phalcon-pecl.tgz" \
    -O phalcon.tgz
tar -xzf phalcon.tgz
# The tarball extracts into a single directory (e.g. cphalcon-5.16.0).
BUILD_DIR="$(find "${WORKDIR}" -maxdepth 1 -type d -name 'cphalcon*' | head -1)"
[ -n "${BUILD_DIR}" ] || BUILD_DIR="${WORKDIR}"

cd "${BUILD_DIR}"

# --- 6. Compile --------------------------------------------------------
echo "==> Compiling Phalcon (this takes a few minutes)..."
"${PHPIZE}"
./configure --with-php-config="${PHPCONFIG}"
make -j"$(nproc)"

# --- 7. Install + enable -----------------------------------------------
echo "==> Installing extension..."
make install

EXT_DIR="$(${PHPCONFIG} --extension-dir)"
INI_DIR="$(${PHPCONFIG} --ini-dir 2>/dev/null || echo /etc/php/${PHP_VERSION}/cli/conf.d)"
mkdir -p "${INI_DIR}"
cat > "${INI_DIR}/30-phalcon.ini" <<EOF
extension=${EXT_DIR}/phalcon.so
EOF

# Also drop the ini where the web SAPI expects it if different.
if [ -d "/etc/php/${PHP_VERSION}/apache2/conf.d" ]; then
    cp "${INI_DIR}/30-phalcon.ini" "/etc/php/${PHP_VERSION}/apache2/conf.d/30-phalcon.ini"
fi
if [ -d "/etc/php/${PHP_VERSION}/fpm/conf.d" ]; then
    cp "${INI_DIR}/30-phalcon.ini" "/etc/php/${PHP_VERSION}/fpm/conf.d/30-phalcon.ini"
fi

# --- 8. Verify ---------------------------------------------------------
echo "==> Verifying..."
if php -m | grep -qi '^phalcon$'; then
    echo "==> SUCCESS: Phalcon ${PHALCON_VERSION} installed for PHP ${PHP_VERSION}."
else
    echo "ERROR: Phalcon installed but not detected by php -m." >&2
    exit 1
fi

# Cleanup
cd /
rm -rf "${WORKDIR}"
echo "==> Done."
