<?php
/**
 * PHP VCS wrapper file system metadata cache metadata handler
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
 * @version $Revision$
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * file system Cache metadata handler.
 */
class vcsCacheFileSystemMetaData extends vcsCacheMetaData
{
    /**
     * A cache file has been
     *
     * Method called, when a cache file has been created. The size can be used
     * to estimate the overall cache size information.
     *
     * If the cleanup() method is cheap in runtime for the cache meta data
     * handler, this method may call the cleanup on every write, or for a
     * meaningful percentage of writes. The cleanup() method will otherwise
     * also be called from outside.
     * 
     * @param string $path 
     * @param int $size 
     * @return void
     */
    public function created( $path, $size )
    {

    }

    /**
     * A cache file has been accessed
     *
     * Method call, when a cache file has been read. This method ist used to
     * basically update the LRU information of cache entries.
     * 
     * @param string $path 
     * @return void
     */
    public function accessed( $path )
    {

    }

    /**
     * Cleanup cache
     *
     * Check if the current cache size exceeds the given requested cache size.
     * If this is the case purge all cache items from the cache until the cache
     * is only filled up to $rate percentage.
     * 
     * @param int $size 
     * @param flaot $rate 
     * @return void
     */
    public function cleanup( $size, $rate )
    {

    }
}
