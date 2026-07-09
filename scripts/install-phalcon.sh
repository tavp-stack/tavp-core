#!/bin/sh
#
# install-phalcon.sh - Install Phalcon 5.x on any Debian/Ubuntu-based
# system (Lando container, VPS, Docker) with a single command.
#
# Dispatched by: tavp phalcon:install
#
# Usage:
#   sudo sh install-phalcon.sh            # auto-detect PHP version
#   sudo sh install-phalcon.sh 8.3        # pin a PHP version
#   sudo sh install-phalcon.sh 8.3 5.16.0 # pin PHP + Phalcon version
#
# Idempotent: exits early if Phalcon is already loaded.

# --- Detect PHP version ------------------------------------------------
if [ -n "${1:-}" ]; then
    PHP_VERSION="$1"
else
    PHP_VERSION="$(php -r 'echo PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION;' 2>/dev/null || echo 8.3)"
fi
PHALCON_VERSION="${2:-5.16.0}"

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

# --- 3. Find phpize and php-config -------------------------------------
# In Lando/Docker, PHP is often compiled from source with tools at
# /usr/local/bin/. On VPS with apt packages, they may be versioned
# (phpize8.3). We prefer whatever is already available.
PHPIZE=""
PHPCONFIG=""

# First: check if plain phpize/php-config work (compiled-from-source)
if command -v phpize >/dev/null 2>&1; then
    PHPIZE="phpize"
    PHPCONFIG="php-config"
fi

# Fallback: versioned names (apt-installed)
if [ -z "$PHPIZE" ] || ! "$PHPIZE" --version >/dev/null 2>&1; then
    if command -v "phpize${PHP_VERSION}" >/dev/null 2>&1; then
        PHPIZE="phpize${PHP_VERSION}"
        PHPCONFIG="php-config${PHP_VERSION}"
    fi
fi

if [ -z "$PHPIZE" ]; then
    echo "ERROR: phpize not found. Install php-dev or php${PHP_VERSION}-dev first." >&2
    exit 1
fi

echo "==> Using: ${PHPIZE}, ${PHPCONFIG}"

# --- 4. Install build dependencies (only if missing) -------------------
# Skip this step if PHP was compiled from source (phpize is at /usr/local/bin)
# and build tools are already present.
NEEDS_APT=0
command -v make >/dev/null 2>&1 || NEEDS_APT=1
command -v gcc >/dev/null 2>&1 || NEEDS_APT=1
command -v pkg-config >/dev/null 2>&1 || NEEDS_APT=1

if [ "$NEEDS_APT" -eq 1 ]; then
    echo "==> Installing build dependencies..."
    export DEBIAN_FRONTEND=noninteractive

    # Detect OS and install appropriate packages
    if [ -f /etc/os-release ]; then
        . /etc/os-release
    fi

    case "${ID:-unknown}" in
        ubuntu|debian|raspbian)
            apt-get update -y
            apt-get install -y \
                build-essential autoconf pkg-config re2c \
                libpcre2-dev zlib1g-dev
            ;;
        *)
            echo "WARNING: untested OS. Install build tools manually." >&2
            ;;
    esac
else
    echo "==> Build tools already present, skipping apt install."
fi

# --- 5. Download source -------------------------------------------------
echo "==> Downloading Phalcon ${PHALCON_VERSION} source..."
WORKDIR="$(mktemp -d)"
cd "${WORKDIR}"
wget -q "https://github.com/phalcon/cphalcon/releases/download/v${PHALCON_VERSION}/phalcon-pecl.tgz" \
    -O phalcon.tgz
tar -xzf phalcon.tgz
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
INI_DIR="$(${PHPCONFIG} --ini-dir 2>/dev/null || echo /usr/local/lib/php/extensions/no-debug-non-zts-20230831)"
mkdir -p "${INI_DIR}"
cat > "${INI_DIR}/30-phalcon.ini" <<EOF
extension=phalcon.so
EOF

# Also enable for any detected PHP SAPI conf.d directories
for sapi in apache2 fpm cli; do
    for dir in "/etc/php/${PHP_VERSION}/${sapi}/conf.d" "/usr/local/etc/php/conf.d"; do
        if [ -d "$dir" ]; then
            cp "${INI_DIR}/30-phalcon.ini" "${dir}/30-phalcon.ini" 2>/dev/null || true
        fi
    done
done

# --- 8. Verify ---------------------------------------------------------
echo "==> Verifying..."
if php -m 2>/dev/null | grep -qi '^phalcon$'; then
    echo "==> SUCCESS: Phalcon ${PHALCON_VERSION} installed for PHP ${PHP_VERSION}."
else
    echo "WARNING: Phalcon compiled but not yet detected by php -m." >&2
    echo "         You may need to restart PHP-FPM or the Lando service." >&2
    echo "         Try: lando restart" >&2
    exit 0
fi

cd /
rm -rf "${WORKDIR}"
echo "==> Done."
