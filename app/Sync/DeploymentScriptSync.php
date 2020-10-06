<?php

namespace App\Sync;

use Illuminate\Console\OutputStyle;
use Laravel\Forge\Resources\Server;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Resources\Webhook;

class DeploymentScriptSync extends BaseSync
{

    public function sync(Server $server, Site $site, OutputStyle $output, bool $force = false): void
    {
        $deploymentScript = join("\n", $this->config->get('deployment', ''));
        $deploymentScriptOnForge = $this->forge->siteDeploymentScript($server->id, $site->id);

        if (!$force && $deploymentScript !== $deploymentScriptOnForge) {
            $output->warning("Skipping the deployment log update, as the script on Forge is different than your local script.\nUse --force to overwrite it.");
            return;
        }

        $this->forge->updateSiteDeploymentScript($server->id, $site->id, $deploymentScript);

        if ($this->config->get('quick-deploy')) {
            $site->enableQuickDeploy();
        } else {
            $site->disableQuickDeploy();
        }
    }
}
