<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{
    private $rules;
    private $incident;

    public function __construct(Incident $incident)
    {
        $this->rules = [
            'title' => 'required',
            'description' => 'max:255',
            'criticality' => 'required',
            'type' => 'required',
            'status' => 'required'
        ];

        $this->incident = $incident;
    }

    public function index()
    {
        $incidents = $this->incident->latest()->paginate(5);
        return \response($incidents);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return $validator->errors();
        } else {
            $incident = $this->incident->create($request->all());
            return \response($incident, 201);
        }
    }

    public function show($id)
    {
        try {
            $incident = $this->incident->findOrFail($id);
            return \response($incident);
        } catch (\Throwable $e) {
            return \response("O incidente ${id} não foi encontrado.", 404);
        }
        $incident = $this->incident->findOrFail($id);
        return \response($incident);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            return $validator->errors();
        } else {
            try {
                $this->incident->findOrFail($id)->update($request->all());
                return \response("Incidente ${id} foi atualizado.", 201);
            } catch (\Throwable $e) {
                return \response("Ocorreu um erro na hora de atualizar.", 500);
            }
        }
    }

    public function destroy($id)
    {
        $incident = $this->incident->find($id);
        $result = $incident->delete();

        if ($result) {
            return \response("Incidente com o id: ${id} foi apagado do sistema.");
        } else {
            return \response("Uma falha ocorreu.", 500);
        }
    }

    public function search($title)
    {
        $incidents = $this->incident->where('title', 'like', '%'.$title.'%')->get();

        if(!empty($incidents)){
            return \response($incidents);
        }
    }
}
