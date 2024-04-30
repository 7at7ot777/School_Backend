<?php

namespace App\Http\Controllers;

use App\Models\TeacherVideo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;

class TeacherVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TeacherVideo $teacherVideo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TeacherVideo $teacherVideo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeacherVideo $teacherVideo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeacherVideo $teacherVideo)
    {
        //
    }

    public function upload(Request $request)
    {
        // التحقق من وجود ملف الفيديو في الطلب
        if ($request->hasFile('video')) {
            // الحصول على الملف الفعلي للفيديو
            $video = $request->file('video');

            // تحديد مسار الحفظ في الخادم
            $path = $video->store('videos', 'public');

            // حفظ بيانات الفيديو في قاعدة البيانات
            $teacherVideo = TeacherVideo::create([
                'video_path' => $path,
                // يمكنك إضافة المزيد من البيانات هنا، مثل اسم الفيديو أو أي معلومات إضافية
            ]);

            // إرجاع رسالة ناجحة
            return response()->json(['message' => 'Video uploaded successfully'], 201);
        } else {
            // في حالة عدم وجود ملف الفيديو في الطلب
            return response()->json(['error' => 'No video file found in the request'], 400);
        }
    }

    /**
     * Return the video.
     */
    public function returnVideo($id)
    {
        // العثور على بيانات الفيديو في قاعدة البيانات
        $teacherVideo = TeacherVideo::findOrFail($id);

        // استرداد المسار للفيديو من قاعدة البيانات
        $videoPath = $teacherVideo->video_path;

        // استخدام Laravel Storage لإرجاع الملف الفعلي للفيديو
        $videoFile = Storage::disk('public')->get($videoPath);

        // استرداد نوع المحتوى للملف
        $mimeType = File::mimeType(storage_path('app/public/' . $videoPath));

        // إعادة الملف كاستجابة مع نوع المحتوى المناسب
        return new Response($videoFile, 200, [
            'Content-Type' => $mimeType
        ]);
    }
}
