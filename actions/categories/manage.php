<?php

use hypeJunction\Categories\Actions\ManageCategories;

$result = hypeCategories()->actions->execute(new ManageCategories);
forward($result->getForwardURL());