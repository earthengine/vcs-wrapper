<?php
/**
 * PHP VCS wrapper Mercurial Cli directory wrapper
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
 * @subpackage MercurialCliWrapper
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/**
 * Directory implementation vor Mercurial Cli wrapper
 *
 * @package VCSWrapper
 * @subpackage MercurialCliWrapper
 * @version $Revision$
 */
class vcsHgCliDirectory extends vcsHgCliResource implements vcsDirectory
{
    /**
     * Store the resources of this directory
     *
     * @var array
     **/
    protected $resources;

    /**
     * Initialize the resources array.
     * 
     * Initilaize the array containing all child elements of the current
     * directly as vcsHgCliResource objects.
     * 
     * @author Tobias Tom <t.tom@succont.de>
     * @uses resources
     * @uses vcsHgCliDirectory
     * @uses vcsHgCliFile
     * @return array(vcsHgCliResource)
     */
    protected function initializeResouces() 
    {
        if ( $this->resources !== null ) {
            return;
        }

        $this->resources = array();

        $contents = dir( $this->root . $this->path );
        while ( ( $path = $contents->read() ) !== false ) {
            if ( in_array( $path, array( '.', '..', '.hg' ) ) ) {
                continue;
            }
    
            $this->resources[] = ( is_dir( $this->root . $this->path . $path ) ?
                new vcsHgCliDirectory( $this->root, $this->path . $path . '/' ) :
                new vcsHgCliFile( $this->root, $this->path . $path )
            );
        }

        $contents->close();
    }

    /**
     * Returns the current item inside this iterator
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @return mixed Current object inside this iterator
     */
    public function current()
    {
        $this->initializeResouces();

        return current( $this->resources );
    }

    /**
     * Returns the next item of the iterator.
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @return mixed Next object inside this iterator
     */
    public function next()
    {
        $this->initializeResouces();

        return next( $this->resources );
    }

    /**
     * Returns the key for the current pointer.
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @return integer Key for the current item
     */
    public function key()
    {
        $this->initializeResouces();

        return key( $this->resources );
    }

    /**
     * Checks if the current item is valid.
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @return boolean Returns TRUE of the current item is valid
     */
    public function valid()
    {
        $this->initializeResouces();

        return $this->current() !== false;
    }
    
    /**
     * Set the internal pointer of an array to its first element.
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @return mixed First element inside this directory
     */
    public function rewind()
    {
        $this->initializeResouces();

        return reset( $this->resources );
    }

    /**
     * Returns the children for this instance.
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @return vcsDirectory Children of this instance
     */
    public function getChildren() 
    {
        $this->initializeResouces();

        return current( $this->resources );
    }
    
    /**
     * Returns if this directory contains files of directories.
     *
     * @author Tobias Tom <t.tom@succont.de>
     * @uses initializeResouces
     * @uses vcsDirectory
     * @return boolean Returns TRUE of it has children, otherwise FALSE
     */
    public function hasChildren()
    {
        $this->initializeResouces();

        return current( $this->resources ) instanceof vcsDirectory;
    }
}
