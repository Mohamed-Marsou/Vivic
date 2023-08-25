<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactUsController extends Controller
{
    public function index()
    {
        try {
            $contacts = Contact::all();
            return response()->json(['contacts' => $contacts], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while fetching contacts', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|email',
                'message' => 'required|string',
            ]);

            $contact = Contact::create($validatedData);

            return response()->json(['message' => 'Contact message added successfully', 'contact' => $contact], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while adding the contact', 'error' => $e->getMessage()], 500);
        }
    }
}