<?php
/**
 * PHP VCS wrapper Mercurial system process class
 *
 * This file is part of \vcs-wrapper.
 *
 * \vcs-wrapper is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * \vcs-wrapper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with \vcs-wrapper; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

namespace Vcs\Wrapper\BzrCli;

use \SystemProcess\SystemProcess;

/**
 * Mercurial executable wrapper for system process class
 *
 * @version $Revision$
 */
class Process extends SystemProcess
{
    /**
     * Static property containg information, if the version of the bzr CLI
     * binary version has already been verified.
     *
     * @var bool
     */
    public static $checked = false;

    /**
     * Class constructor taking the executable.
     * 
     * @param string $executable
     */
    public function __construct( $executable = 'bzr' ) 
    {
        parent::__construct( $executable );

        self::checkVersion();

        $this->nonZeroExitCodeException = false;
    }


    /**
     * Verify bzr version
     *
     * Verify that the version of the installed bzr binary is at least 1.1. Will
     * throw an exception, if the binary is not available or too old.
     * 
     * @return void
     */
    protected static function checkVersion()
    {
        if ( self::$checked === true )
        {
            return true;
        }

        $process = new SystemProcess( 'bzr' );
        $process->nonZeroExitCodeException = true;
        $process->argument( '--version' )->execute();

        if ( !preg_match( '/\Bazaar \(bzr\) ([0-9.]*)/', $process->stdoutOutput, $match ) )
        {
            throw new \RuntimeException( 'Could not determine Bazaar version.' );
        }

        if ( version_compare( $match[1], '1.1', '<' ) )
        {
            throw new \RuntimeException( 'Bazaar is required in a minimum version of 1.1.' );
        }

        $process = new SystemProcess( 'bzr' );
        $process->nonZeroExitCodeException = true;
        $process->argument( 'plugins' )->execute();

        if ( strpos( $process->stdoutOutput, 'xmloutput' ) === false )
        {
            throw new \RuntimeException( 'Missing required bazaar pluging "xmloutput".' );
        }

        return self::$checked = true;
    }
}

