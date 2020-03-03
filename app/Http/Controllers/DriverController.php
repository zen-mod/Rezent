<?php

namespace App\Http\Controllers;

use App\Driver;
use App\Drivers\ActionsDriver;
use App\Drivers\PipelinesDriver;
use App\Drivers\TravisDriver;
use Exception;
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

        $driver = $this->driver($driverName);

        $validated = $driver->validate($request);

        $driver = $driver->create($validated);

        return response()->json([
            'successful' => $driver->send(),
        ]);
    }

    /**
     * Ensures a valid, supported driver is used.
     *
     * @param string $name
     * @throws Illuminate\Http\Exceptions\HttpResponseException
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
                abort(400, 'Unknown build driver.');
        }
    }

    protected function driver(string $name): Driver
    {
        switch ($name) {
            case 'pipelines':
                return new PipelinesDriver();

            case 'travis':
                return new TravisDriver();

            case 'actions':
                return new ActionsDriver();

            default:
                throw new Exception('Unknown driver to create.');
        }
    }
}
