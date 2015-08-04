<?php

use hypeJunction\Categories\Actions\SavePluginSettings;

$result = hypeApps()->actions->execute(new SavePluginSettings());
forward($result->getForwardURL());