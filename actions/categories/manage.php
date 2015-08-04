<?php

use hypeJunction\Categories\Actions\ManageCategories;

$result = hypeApps()->actions->execute(new ManageCategories());
forward($result->getForwardURL());