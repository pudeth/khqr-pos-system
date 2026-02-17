@extends('layouts.admin')

@section('title', 'Pospay Categories')
@section('header', 'CATEGORIES MANAGEMENT')

@section('content')
<div class="mb-6">
    <button onclick="openAddModal()" class="neo-btn bg-neo-purple text-black px-8 py-4 rounded-xl font-black text-lg">
        <i class="fas fa-plus mr-2"></i>ADD CATEGORY
    </button>
</div>

<div class="neo-card bg-white rounded-xl overflow-hidden">
    <div class="bg-gradient-to-r from-pink-400 to-orange-400 p-6 border-b-4 border-black">
        <h3 class="text-2xl font-black text-white uppercase tracking-wide">All Categories</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full neo-table">
            <thead>
                <tr>
                    <th class="px-6 py-4 text-left">Name</th>
                    <th class="px-6 py-4 text-left">Description</th>
                    <th class="px-6 py-4 text-left">Products</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr class="transition-all duration-200">
                    <td class="px-6 py-4 font-black text-lg">{{ $category->name }}</td>
                    <td class="px-6 py-4 font-bold">{{ $category->description }}</td>
                    <td class="px-6 py-4">
                        <span class="neo-badge bg-neo-blue px-4 py-2 rounded-lg inline-block text-lg">
                            {{ $category->products_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="neo-badge {{ $category->is_active ? 'bg-neo-green' : 'bg-gray-300' }} px-3 py-1 rounded-lg inline-block">
                            {{ $category->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick='editCategory(@json($category))' class="neo-btn bg-neo-yellow text-black px-4 py-2 rounded-lg mr-2">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="/admin/categories/{{ $category->id }}" method="POST" class="inline" onsubmit="return confirm('Delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="neo-btn bg-neo-pink text-black px-4 py-2 rounded-lg">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="neo-card bg-white rounded-xl p-8 w-full max-w-md">
        <h3 id="modalTitle" class="text-3xl font-black mb-6 uppercase">Add Category</h3>
        <form id="categoryForm" method="POST" action="/admin/categories">
            @csrf
            <input type="hidden" id="categoryId" name="_method" value="POST">
            
            <div class="mb-6">
                <label class="block text-sm font-black mb-2 uppercase">Name *</label>
                <input type="text" name="name" id="name" required class="neo-input w-full rounded-lg px-4 py-3 font-bold">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-black mb-2 uppercase">Description</label>
                <textarea name="description" id="description" rows="3" class="neo-input w-full rounded-lg px-4 py-3 font-bold"></textarea>
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-6 h-6 mr-3 border-3 border-black">
                    <span class="text-sm font-black uppercase">Active</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeModal()" class="neo-btn bg-gray-300 text-black px-6 py-3 rounded-lg">CANCEL</button>
                <button type="submit" class="neo-btn bg-neo-green text-black px-8 py-3 rounded-lg">SAVE</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'ADD CATEGORY';
    document.getElementById('categoryForm').action = '/admin/categories';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = 'POST';
    document.getElementById('categoryModal').classList.remove('hidden');
}

function editCategory(category) {
    document.getElementById('modalTitle').textContent = 'EDIT CATEGORY';
    document.getElementById('categoryForm').action = '/admin/categories/' + category.id;
    document.getElementById('categoryId').value = 'PUT';
    document.getElementById('name').value = category.name;
    document.getElementById('description').value = category.description || '';
    document.getElementById('is_active').checked = category.is_active;
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}
</script>
@endpush
@endsection
