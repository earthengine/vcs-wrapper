<?php
/**
 * PHP VCS wrapper abstract file base class
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
 * @package Core
 * @version $Revision: 4 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Base class for resources in the VCS wrapper.
 *
 * This class works as a base class for file and directory resources in the
 * wrapper implementations.
 */
abstract class vcsResource
{
    /**
     * Local repository path
     * 
     * @var string
     */
    protected $path;

    /**
     * Construct file from local repository path
     * 
     * @param mixed $path 
     * @return void
     */
    abstract public function __construct( $path );

    /**
     * String conversion method
     *
     * When a resources is casted to a string, return the local repository path
     * of the resource.
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->path;
    }
}
