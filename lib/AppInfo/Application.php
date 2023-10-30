<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Jharby <jsara030@fiu.edu>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NextOPN\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

class Application extends App implements IBootstrap {
	public const APP_ID = 'nextopn';
	public const DEFAULT_OPNSENSE_URL = 'https://35.199.16.187';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
	public function register(IRegistrationContext $context): void {
	}

	public function boot(IBootContext $context): void {
	}
}
