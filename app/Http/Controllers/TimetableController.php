<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Employee;
use App\Models\TimeTable;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function getDataForMakeTable()
    {
        $teachers =  $this->getAllTeachersFormated();
        $classrooms= $this->getAllClasses();
        $periods = [1,2,3,4,5];
        $days = TimeTable::$DAYS;
        $obj = new \stdClass();
        $obj->teachers = $teachers;
        $obj->classrooms = $classrooms;
        $obj->periods = $periods;
        $obj->days = $days;


        return response()->json($obj);
    }

    public function addNewPeriod(Request $request){
//        return $request->all();

        if($this->saveUniqueEntry($request))
        {
            return response()->json(['error' => 'The Period is duplicated']);

        }
        $newTimetableEntry = Timetable::create([
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,
            'day' => $request->day,
            'period' => $request->period,
        ]);
        $newTimetableEntry->save();
        return response()->json(['success' => 'Period is added successfully']);
    }


    private function getAllTeachersFormated()
    {
        $teachers = Employee::with('subject:id,name', 'user:id,name')
            ->where('role', 'teacher')
            ->whereHas('user', function ($query) {
                $query->where('status', 1);
            })->get();

        $formattedTeachers = $teachers->map(function ($teacher) {
            return [
                'teacher_id' => $teacher->id,
                'id' => $teacher->user->id,
                'name' => $teacher->user->name,
                'subjects' => $teacher->subject->map(function ($subject) {
                    return [
                        'id' => $subject->id,
                        'name' => $subject->name,
                    ];
                })
            ];
        });
        return $formattedTeachers;
    }

    private function getAllClasses(){
      return ClassRoom::select('id','class_number','grade')->get();
    }

    public function getTeacherTable($teacherId)
    {
        $timetableEntry = Timetable::with('subject:id,name','class:id,class_number,grade')
            ->where('teacher_id',$teacherId)->get();
        $timetableEntry->collect();

        $timetableEntry = $timetableEntry->map(function ($item, $key) {
            return [
                'id' => $item['id'],
                'day' => $item['day'] ,
                'period' => $item['period'],
                'data'=>[
                    'sub' => $item['subject']['name'],
                    'class' => $item['class']['grade']  . "/" . $item['class']['class_number']
                ],
            ];
        });

        return response()->json($timetableEntry);

    }

    public function getClassTable($classId)
    {
        $timetableEntry = Timetable::with('teacher.user:id,name','subject:id,name','class:id,class_number,grade')
            ->where('class_id',$classId)
            ->get();

        $transformedData = [];

        foreach ($timetableEntry as $entry)  {
            $transformedData[] = [
                'id' => $entry->id,
                'day' => $entry->day,
                'period' => $entry->period,
                'data' => [
                    'sub' => $entry->subject->name,
                    'class' => $entry->class->grade . '\/' . $entry->class->class_number
                ]
            ];
        }

        return response()->json($transformedData);
    }

    public function saveUniqueEntry($data)
    {
        return TimeTable::where('period', $data['period'])
            ->where('day', $data['day'])
            ->where('class_id', $data['class_id'])
            ->exists()
            ||
            TimeTable::where('period', $data['period'])
                ->where('day', $data['day'])
                ->where('teacher_id', $data['teacher_id'])
                ->exists()
            ;

        // TODO: OR teacher and day and period exists
    }

    public function editPeriod(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'teacher_id' => 'required',
            'subject_id' => 'required',
            'class_id' => 'required',
            'day' => 'required',
            'period' => 'required',
        ]);

        // Check if the combination is unique except for the current record
        if ($this->saveUniqueEntry($request)) {
            return response()->json(['error' => 'The period is duplicated.'], 422);
        }

        // Find the Timetable entry
        $timetableEntry = Timetable::findOrFail($id);

        // Update the fields
        $timetableEntry->update([
            'teacher_id' => $request->teacher_id,
            'subject_id' => $request->subject_id,
            'class_id' => $request->class_id,
            'day' => $request->day,
            'period' => $request->period,
        ]);

        return response()->json(['success' => 'Period updated successfully']);
    }

    public function deletePeriod($id)
    {
        // Find the Timetable entry
        $timetableEntry = Timetable::find($id);
        if(!$timetableEntry)
            return response()->json(['error' => 'The period is not found.'], 404);


        // Delete the entry
        $timetableEntry->delete();

        return response()->json(['success' => 'Period deleted successfully']);
    }

    public function removeSubjectFromClass($class_id,$subject_id)
    {
             TimeTable::where('class_id',$class_id)
            ->where('subject_id',$subject_id)
            ->delete();
            return response()->json(['success' => 'Deleted Successfully.'], 200);


    }

}