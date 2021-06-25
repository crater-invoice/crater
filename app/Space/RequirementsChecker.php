<?php

namespace Crater\Space;

use Illuminate\Support\Str;
use PDO;
use SQLite3;

class RequirementsChecker
{
    /**
     * Minimum PHP Version Supported (Override is in installer.php config file).
     *
     * @var _minPhpVersion
     */
    private $_minPhpVersion = '7.0.0';

    /**
     * Check for the server requirements.
     *
     * @param array $requirements
     * @return array
     */
    public function check(array $requirements)
    {
        $results = [];

        foreach ($requirements as $type => $requirement) {
            switch ($type) {
                // check php requirements
                case 'php':
                    foreach ($requirements[$type] as $requirement) {
                        $results['requirements'][$type][$requirement] = true;

                        if (! extension_loaded($requirement)) {
                            $results['requirements'][$type][$requirement] = false;

                            $results['errors'] = true;
                        }
                    }

                    break;
                // check apache requirements
                case 'apache':
                    foreach ($requirements[$type] as $requirement) {
                        // if function doesn't exist we can't check apache modules
                        if (function_exists('apache_get_modules')) {
                            $results['requirements'][$type][$requirement] = true;

                            if (! in_array($requirement, apache_get_modules())) {
                                $results['requirements'][$type][$requirement] = false;

                                $results['errors'] = true;
                            }
                        }
                    }

                    break;
            }
        }

        return $results;
    }

    /**
     * Check PHP version requirement.
     *
     * @return array
     */
    public function checkPHPVersion(string $minPhpVersion = null)
    {
        $minVersionPhp = $minPhpVersion;
        $currentPhpVersion = $this->getPhpVersionInfo();
        $supported = false;

        if ($minPhpVersion == null) {
            $minVersionPhp = $this->getMinPhpVersion();
        }

        if (version_compare($currentPhpVersion['version'], $minVersionPhp) >= 0) {
            $supported = true;
        }

        $phpStatus = [
            'full' => $currentPhpVersion['full'],
            'current' => $currentPhpVersion['version'],
            'minimum' => $minVersionPhp,
            'supported' => $supported,
        ];

        return $phpStatus;
    }

    /**
     * Get current Php version information.
     *
     * @return array
     */
    private static function getPhpVersionInfo()
    {
        $currentVersionFull = PHP_VERSION;
        preg_match("#^\d+(\.\d+)*#", $currentVersionFull, $filtered);
        $currentVersion = $filtered[0];

        return [
            'full' => $currentVersionFull,
            'version' => $currentVersion,
        ];
    }

    /**
     * Get minimum PHP version ID.
     *
     * @return string _minPhpVersion
     */
    protected function getMinPhpVersion()
    {
        return $this->_minPhpVersion;
    }

    /**
     * Check PHP version requirement.
     *
     * @return array
     */
    public function checkMysqlVersion($conn)
    {
        $version_info = $conn->getAttribute(PDO::ATTR_SERVER_VERSION);

        $isMariaDb = Str::contains($version_info, 'MariaDB');

        $minVersionMysql = $isMariaDb ? config('crater.min_mariadb_version') : config('crater.min_mysql_version');

        $currentMysqlVersion = $this->getMysqlVersionInfo($conn);

        $supported = false;

        if (version_compare($currentMysqlVersion, $minVersionMysql) >= 0) {
            $supported = true;
        }

        $phpStatus = [
            'current' => $currentMysqlVersion,
            'minimum' => $minVersionMysql,
            'supported' => $supported,
        ];

        return $phpStatus;
    }

    /**
     * Get current Mysql version information.
     *
     * @return string
     */
    private static function getMysqlVersionInfo($pdo)
    {
        $version = $pdo->query('select version()')->fetchColumn();

        preg_match("/^[0-9\.]+/", $version, $match);

        return $match[0];
    }

    /**
     * Check Sqlite version requirement.
     *
     * @return array
     */
    public function checkSqliteVersion(string $minSqliteVersion = null)
    {
        $minVersionSqlite = $minSqliteVersion;
        $currentSqliteVersion = $this->getSqliteVersionInfo();
        $supported = false;

        if (version_compare($currentSqliteVersion, $minVersionSqlite) >= 0) {
            $supported = true;
        }

        $phpStatus = [
            'current' => $currentSqliteVersion,
            'minimum' => $minVersionSqlite,
            'supported' => $supported,
        ];

        return $phpStatus;
    }

    /**
     * Get current Sqlite version information.
     *
     * @return string
     */
    private static function getSqliteVersionInfo()
    {
        $currentVersion = SQLite3::version();

        return $currentVersion['versionString'];
    }

    /**
     * Check Pgsql version requirement.
     *
     * @return array
     */
    public function checkPgsqlVersion($conn, string $minPgsqlVersion = null)
    {
        $minVersionPgsql = $minPgsqlVersion;
        $currentPgsqlVersion = $this->getPgsqlVersionInfo($conn);
        $supported = false;

        if (version_compare($currentPgsqlVersion, $minVersionPgsql) >= 0) {
            $supported = true;
        }

        $phpStatus = [
            'current' => $currentPgsqlVersion,
            'minimum' => $minVersionPgsql,
            'supported' => $supported,
        ];

        return $phpStatus;
    }

    /**
     * Get current Pgsql version information.
     *
     * @return string
     */
    private static function getPgsqlVersionInfo($conn)
    {
        $currentVersion = pg_version($conn);

        return $currentVersion['server'];
    }
}
