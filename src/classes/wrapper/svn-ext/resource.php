<?php
/**
 * PHP VCS wrapper SVN Ext resource wrapper
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
 * @subpackage SvnExtWrapper
 * @version $Revision: 10 $
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
 */

/*
 * Resource implementation vor SVN Ext wrapper
 */
abstract class vcsSvnExtResource extends vcsResource implements vcsVersioned, vcsAuthored, vcsLogged
{
    /**
     * Current version of the given resource
     * 
     * @var string
     */
    protected $currentVersion = null;

    /**
     * Get resource log
     *
     * Get the full log for the current resource up tu the current revision
     *
     * @return vcsXml
     */
    protected function getResourceLog()
    {
        if ( ( $log = vcsCache::get( $this->path, $this->currentVersion, 'log' ) ) === false )
        {
            $svnLog = svn_log( $this->root . $this->path );

            $log = array();
            foreach ( $svnLog as $nr => $entry )
            {
                $log[$entry['rev']] = new vcsLogEntry(
                    $entry['rev'],
                    $entry['author'],
                    $entry['msg'],
                    strtotime( $entry['date'] )
                );
            }
            uksort( $log, array( $this, 'compareVersions' ) );
            $last = end( $log );

            vcsCache::cache( $this->path, $this->currentVersion = (string) $last->version, 'log', $log );
        }

        return $log;
    }

    /**
     * Get resource property
     *
     * Get the value of an SVN property
     *
     * @return string
     */
    protected function getResourceProperty( $property )
    {
        // There currently seems no way to get the property contents inside a
        // checkout.
        return null;

        if ( ( $value = vcsCache::get( $this->path, $this->currentVersion, $property ) ) === false )
        {
            $rep   = svn_repos_open( $this->root );
            $value = svn_fs_node_prop( $rep, $this->path, 'svn:' . $property );
            vcsCache::cache( $this->path, $this->currentVersion, $property, $value );
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getVersionString()
    {
        $log  = $this->getResourceLog();
        $last = end( $log );

        return $last->version;
    }

    /**
     * @inheritdoc
     */
    public function getVersions()
    {
        $versions = array();
        $log = $this->getResourceLog();
        foreach ( $log as $entry )
        {
            $versions[] = (string) $entry->version;
        }

        return $versions;
    }

    /**
     * @inheritdoc
     */
    public static function compareVersions( $version1, $version2 )
    {
        return $version1 - $version2;
    }

    /**
     * @inheritdoc
     */
    public function getAuthor( $version = null )
    {
        $log  = $this->getResourceLog();
        $last = end( $log );

        return $last->author;
    }

    /**
     * @inheritdoc
     */
    public function getLog()
    {
        return $this->getResourceLog();
    }

    /**
     * @inheritdoc
     */
    public function getLogEntry( $version )
    {
        $log = $this->getResourceLog();

        if ( !isset( $log[$version] ) )
        {
            throw new vcsNoSuchVersionException( $this->path, $version );
        }

        return $log[$version];
    }
}
