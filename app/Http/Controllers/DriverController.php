<?php

namespace App\Http\Controllers;

use App\Drivers\ActionsDriver;
use App\Drivers\PipelinesDriver;
use App\Drivers\TravisDriver;
use App\Exceptions\DriverNotFound;
use App\Jobs\SendBuildNotification;
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
        $driverClassPath = $this->getValidDriver($driverName);

        $validated = $driverClassPath::validate($request);

        SendBuildNotification::dispatch($driverClassPath, $validated);

        return response()->json([
            'queued' => true,
        ]);
    }

    /**
     * Ensures a valid, supported driver is used and returns the driver class name path.
     *
     * @param string $name
     * @return string
     * @throws App\Exceptions\DriverNotFound
     */
    protected function getValidDriver(string $name): string
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
