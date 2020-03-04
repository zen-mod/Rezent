<?php

namespace App\Http\Controllers;

use App\Drivers\ActionsDriver;
use App\Drivers\PipelinesDriver;
use App\Drivers\TravisDriver;
use App\Exceptions\DriverNotFound;
use App\Notifications\BuildNotification;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    /**
     * Handles authorization for this controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Identifies the driver to use and creates the respective Driver model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string $driverName
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function __invoke(Request $request, string $driverName)
    {
        $this->ensureValidDriver($driverName);

        $driverClassPath = $this->driver($driverName);

        $validated = $driverClassPath::validate($request);

        /**
         * @var \App\Driver\Driver $driver
         */
        $driver = new $driverClassPath($validated);

        $ignored = true;

        if (!$driver->wasAlreadySent()) {
            $driver->notify(new BuildNotification());

            $ignored = false;
        }

        return response()->json([
            'successful' => true,
            'ignored' => $ignored,
        ]);
    }

    /**
     * Ensures a valid, supported driver is used.
     *
     * @param string $name
     * @throws App\Exceptions\DriverNotFound
     */
    protected function ensureValidDriver(string $name)
    {
        switch ($name) {
            case 'pipelines':
                break;

            case 'travis':
                break;

            case 'actions':
                break;

            default:
                throw new DriverNotFound();
        }
    }

    /**
     * Returns a class name path to the driver to use.
     *
     * @param string $name Driver name.
     * @return string Class name path.
     * @throws App\Exceptions\DriverNotFound Unknown driver found.
     */
    protected function driver(string $name): string
    {
        switch ($name) {
            case 'pipelines':
                return PipelinesDriver::class;

            case 'travis':
                return TravisDriver::class;

            case 'actions':
                return ActionsDriver::class;

            default:
                throw new DriverNotFound();
        }
    }
}
