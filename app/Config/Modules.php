<?php

namespace Config;

use CodeIgniter\Modules\Modules as BaseModules;

class Modules extends BaseModules
{
    /**
     * --------------------------------------------------------------------------
     * Enable Auto-Discovery?
     * --------------------------------------------------------------------------
     *
     * If true, then auto-discovery will happen across all elements listed in
     * $activeExplorers below. If false, no auto-discovery will happen at all,
     * giving a slight performance boost.
     */
    public $enabled = true;

    /**
     * --------------------------------------------------------------------------
     * Auto-Discovery Within Composer Packages
     * --------------------------------------------------------------------------
     *
     * If true, then auto-discovery will happen across all namespaces loaded
     * by Composer, as well as the namespaces configured locally.
     */
    public $discoverInComposer = true;

    /**
     * --------------------------------------------------------------------------
     * Auto-discover Rules
     * --------------------------------------------------------------------------
     *
     * Lists the aliases of all discovery classes that will be active
     * and used during the current application request.
     */
    public $aliases = [
        'events',
        'registrars',
        'routes',
        'services',
    ];

    /**
     * --------------------------------------------------------------------------
     * Composer Packages
     * --------------------------------------------------------------------------
     *
     * A list of composer packages to auto-discover from.
     */
    public $composerPackages = [];
}
