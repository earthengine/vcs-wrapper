<?php
/**
 * PHP VCS wrapper SVN-Cli based repository wrapper
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
 * @package VCSWrapper
 * @subpackage SvnCliWrapper
 * @version $Revision: 10 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Handler for SVN repositories
 */
class vcsSvnCliRepository extends vcsSvnCliDirectory implements vcsRepository
{
    /**
     * Aggegated vcs directory object
     * 
     * @var vcsDirectory
     */
    protected $directory = null;

    /**
     * Construct repository with repository root path
     *
     * Construct the repository with the repository root path, which will be
     * used to store the repository contents.
     *
     * @param string $root 
     * @return void
     */
    public function __construct( $root )
    {
        parent::__construct( $root, '/' );
    }

    /**
     * Initialize repository
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
        $process = new pbsSystemProcess( 'svn' );
        $process->argument( '--non-interactive' );

        if ( $user !== null )
        {
            $process->argument( '--username' )->argument( $user );

            if ( $password !== null )
            {
                $process->argument( '--password' )->argument( $password );
            }
        }

        $return = $process->argument( 'checkout' )->argument( $url )->argument( $this->root )->execute();

        if ( $return !== 0 )
        {
            throw new vcsRpositoryInitialisationFailedException( $process->stdoutOutput );
        }

        // Cache basic revision information for checkout and update
        // currentVersion property.
        $this->getResourceInfo();
    }

    /**
     * Update repository
     *
     * Update the repository to the most current state.
     *
     * Optionally a version can be specified, in which case the repository
     * won't be updated to the latest version, but to the specified one.
     * 
     * @param string $version
     * @return void
     */
    public function update( $version = null )
    {
        $process = new pbsSystemProcess( 'svn' );
        $process->argument( '--non-interactive' );

        if ( $version !== null )
        {
            $process->argument( '-r' . $version );
        }

        $return = $process->argument( 'update' )->argument( $this->root )->execute();

        if ( $return !== 0 )
        {
            throw new vcsRpositoryUpdateFailedException( $process->stdoutOutput );
        }

        // Cache basic revision information for checkout and update
        // currentVersion property.
        $this->getResourceInfo();
    }
}

