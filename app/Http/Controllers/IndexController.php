<?php

namespace VKMUSIC\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use VKMUSIC\Http\Controllers\Auth\VkController;
use VKMUSIC\Http\Requests;
use VKMUSIC\VkFile;

class IndexController extends Controller
{
    /**
     * Основная страница.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-05
     * @since   1.0
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getIndex()
    {
        return view('index');
    }

    /**
     * Страница подтверждения доступа.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-06
     * @since   1.0
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getVerify(Request $request)
    {
        if ($request->has('code')) {
            $vkController = new VkController();
            $response     = $vkController->verifyCode($request);
            $content      = json_decode($response->content());
            $statusCode   = $response->getStatusCode();

            if ($statusCode == 200) {
                return redirect()->route('index');
            }
        }

        return view('verify')->withErrors($content->error->error_description);
    }

    /**
     * Скачивание файла с инкрементом счетчика.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-07
     * @since   1.0
     *
     * @param $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getDownloadFile($id = null)
    {
        $validator = \Validator::make([
            'id' => $id,
        ], [
            'id' => 'required|numeric|min:1|exists:vk_files',
        ]);

        if ($validator->fails()) {
            return abort(404);
        }

        $file = VkFile::whereId($id)->where('expired_at', '>', Carbon::now())->first();

        if (is_null($file)) {
            return abort(404);
        }

        if (!\Storage::disk('mp3')->exists($file->filename)) {
            return abort(404);
        }

        $file->downloads++;
        $file->save();

        $url = config('filesystems.disks.mp3.root') . '\\' . $file->filename;

        return response()->download($url, $file->title, [
            'Content-Type' => 'audio/mpeg',
        ]);
    }

    /**
     * Организуем выход юзера из системы.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-08
     * @since   1.0
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getLogout()
    {
        if (\Auth::check()) {
            \Auth::logout();
        }

        return redirect()->route('index');
    }
}
