<?php

namespace App\Http\Controllers\Admin;

use App\Filters\SystemMediaFilter;
use App\Http\Requests\SystemMediaRequest;
use App\Http\Resources\SystemMediaResource;
use App\Models\SystemMedia;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SystemMediaController extends AdminBaseController
{
    public function destroy(SystemMedia $systemMedia)
    {
        try {
            $systemMedia->delete();
        } catch (FileException $e) {
            return $this->error($e->getMessage());
        }
        return $this->noContent();
    }

    public function edit(SystemMedia $systemMedia)
    {
        return $this->ok(SystemMediaResource::make($systemMedia));
    }

    public function update(SystemMediaRequest $request, SystemMedia $systemMedia)
    {
        $inputs = $request->validated();
        $systemMedia->update($inputs);
        return $this->created($systemMedia);
    }

    public function index(SystemMediaFilter $filter)
    {
        $media = SystemMedia::query()
            ->filter($filter)
            ->orderByDesc('id')
            ->paginate();

        return $this->ok(SystemMediaResource::collection($media));
    }

    public function batchUpdate(SystemMediaRequest $request)
    {
        $inputs = $request->validated();

        SystemMedia::query()
            ->whereIn('id', $request->input('id', []))
            ->update($inputs);

        return $this->created();
    }

    public function batchDestroy(Request $request)
    {
        try {
            SystemMedia::query()
                ->whereIn('id', $request->input('id', []))
                ->get()
                ->each
                ->delete();
        } catch (FileException $e) {
            return $this->error($e->getMessage());
        }

        return $this->noContent();
    }
}
