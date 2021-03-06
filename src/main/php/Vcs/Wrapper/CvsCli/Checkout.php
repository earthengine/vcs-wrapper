<?php
/**
 * PHP VCS wrapper CVS-Cli based repository wrapper
 *
 * This file is part of vcs-wrapper.
 *
 * vcs-wrapper is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Lesser General Public License as published by the Free
 * Software Foundation; version 3 of the License.
 *
 * vcs-wrapper is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public License for
 * more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with vcs-wrapper; if not, write to the Free Software Foundation, Inc., 51
 * Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

namespace Vcs\Wrapper\CvsCli;

use \Vcs\FileNotFoundException;
use \Vcs\InvalidRepositoryUrlException;

/**
 * Handler for CVS repositories
 *
 * @version $Revision$
 */
class Checkout extends Directory implements \Vcs\Checkout
{
    /**
     * Construct checkout with the given root path.
     *
     * Construct the checkout with the given root path, which will be used to
     * store the repository contents.
     *
     * @param string $root
     */
    public function __construct( $root )
    {
        parent::__construct( $root, '/' );
    }

    /**
     * Initializes fresh checkout
     *
     * Initialize repository from the given URL. Optionally username and
     * password may be passed to the method, if required for the repository.
     *
     * @param string $url
     * @param string $user
     * @param string $password
     * @return void
     */
    public function initialize( $url, $user = null, $password = null )
    {
        $count = substr_count( $url, '#' );
        if ( $count === 1 )
        {
            $revision = null;
            list( $repoUrl, $module ) = explode( '#', $url );
        }
        else if ( $count === 2 )
        {
            list( $repoUrl, $module, $revision ) = explode( '#', $url );
        }
        else
        {
            throw new InvalidRepositoryUrlException( $url, 'cvs' );
        }

        $process = new Process();
        $process
            ->argument( '-d' )
            ->argument( $repoUrl )
            ->argument( 'checkout' )
            ->argument( '-P' )
            ->argument( '-r' )
            ->argument( $revision )
            ->argument( '-d' )
            ->argument( $this->root )
            ->argument( $module )
            ->execute();
    }


    /**
     * Update repository
     *
     * Update the repository to the most current state. Method will return
     * true, if an update happened, and false if no update was available.
     *
     * Optionally a version can be specified, in which case the repository
     * won't be updated to the latest version, but to the specified one.
     *
     * @param string $version
     * @return bool
     */
    public function update( $version = null )
    {
        if ( $version === null )
        {
            $version = 'HEAD';
        }

        $process = new Process();
        $process
            ->workingDirectory( $this->root )
            ->redirect( Process::STDERR, Process::STDOUT )
            ->argument( 'update' )
            ->argument( '-Rd' )
            ->argument( '-r' )
            ->argument( $version )
            ->execute();

        return ( preg_match( '#[\n\r]U #', $process->stdoutOutput ) > 0 );
    }

    /**
     * Get checkout item
     *
     * Get an item from the checkout, specified by its local path. If no item
     * with the specified path exists an exception is thrown.
     *
     * Method either returns a \Vcs\Checkout, a \Vcs\Directory or a \Vcs\File
     * instance, depending on the given path.
     * 
     * @param string $path
     * @return mixed
     */
    public function get( $path = '/' )
    {
        $fullPath = realpath( $this->root . $path );

        if ( ( $fullPath === false ) ||
             ( strpos( $fullPath, $this->root ) !== 0 ) )
        {
            throw new FileNotFoundException( $path );
        }

        switch ( true )
        {
            case ( $path === '/' ):
                return $this;

            case is_dir( $fullPath ):
                return new Directory( $this->root, $path );

            default:
                return new File( $this->root, $path );
        }
    }
}

