<h1>{{ $category->name }} ({{ $category->code }})</h1> <hr>

<h2>Item yang Memiliki Kategori Ini:</h2>
<ul>
    @forelse ($category->masterItems as $item)
        <li>{{ $item->item_code }} - {{ $item->item_name }}</li>
    @empty
        <li>Tidak ada item dalam kategori ini.</li>
    @endforelse
</ul>

<div class="form-group">
    <label for="categories">Kategori Items</label>
    <select name="categories[]" id="categories" class="form-control" multiple>
        @foreach($allCategories as $category)
            <option value="{{ $category->id }}" 
                @if(isset($masterItem) && $masterItem->categories->contains($category->id)) 
                    selected 
                @endif>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>