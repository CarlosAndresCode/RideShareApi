<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateDriverRequest;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load('driver');
        return $user;
    }

    public function update(UpdateDriverRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        $user->update($request->only('name'));

        $user->driver()
            ->updateOrCreate($request->only(['year', 'make', 'model', 'color', 'license_plate']));

        $user->load('driver');

        return $user;
    }
}
