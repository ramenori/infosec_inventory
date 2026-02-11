<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactPerson;

class ContactPersonController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $contacts = ContactPerson::when($search, function($query) use ($search) {
            return $query->where('name', 'like', "%{$search}%")
                        ->orWhere('contact_number', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('satellite_office', 'like', "%{$search}%");
        })->paginate(10);

        return view('admin.contactperson', compact('contacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'satellite_office' => 'nullable|string|max:255',
        ]);

        ContactPerson::create($request->all());

        return redirect()->route('admin.contactperson')->with('success', 'Contact person added successfully!');
    }

    public function update(Request $request, $id)
    {
        $contact = ContactPerson::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'satellite_office' => 'nullable|string|max:255',
        ]);

        $contact->update($request->all());

        return redirect()->route('admin.contactperson')->with('success', 'Contact person updated successfully!');
    }

    public function destroy($id)
    {
        $contact = ContactPerson::findOrFail($id);
        $contact->delete();

        return redirect()->route('admin.contactperson')->with('success', 'Contact person deleted successfully!');
    }
}