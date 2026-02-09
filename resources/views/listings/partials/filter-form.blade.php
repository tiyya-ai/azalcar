<form action="{{ route('listings.search') }}" method="GET" class="search-filter-form">
    <div class="keyword-input-wrapper">
        <input type="text" name="q" value="{{ request('q') }}" class="keyword-input" placeholder="Try great deals under $20k">
        <button type="submit" class="keyword-btn"><i class="fas fa-search"></i></button>
    </div>
    
    <!-- Applied Filters Section -->
    <div class="applied-filters-area">
        @foreach(request()->all() as $key => $value)
            @if($value && $key != 'page' && !is_array($value) && $key != 'q')
                <a href="{{ route('listings.search', request()->except($key)) }}" class="filter-pill">
                    {{ ucwords(str_replace('_', ' ', $key)) }}
                    <i class="fas fa-times"></i>
                </a>
            @endif
        @endforeach
    </div>

    <!-- Basics -->
    <div class="filter-section first">
        <h3 class="filter-header">Basics</h3>
        <div style="margin-bottom: 20px;">
            <label class="radio-item">
                <input type="radio" name="condition" value="new" {{ request('condition') == 'new' ? 'checked' : '' }} onchange="this.form.submit()"> New
            </label>
            <label class="radio-item" style="margin-top: 8px;">
                <input type="radio" name="condition" value="used" {{ request('condition') == 'used' ? 'checked' : '' }} onchange="this.form.submit()"> Used
            </label>
        </div>
    </div>

    <!-- Make & Model -->
    <div class="filter-section">
        <h3 class="filter-header">Make, Model & Trim</h3>
        <div class="mb-12" style="margin-bottom: 12px;">
            <select name="make" class="c-select make-select">
                <option value="">All Makes</option>
                @foreach($makes as $make)
                    <option value="{{ $make->slug }}" {{ request('make') == $make->slug ? 'selected' : '' }}>{{ $make->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-12">
            <select name="model" class="c-select model-select">
                <option value="">All Models</option>
            </select>
        </div>
    </div>

    <!-- Price & Payment -->
    <div class="filter-section">
        <h3 class="filter-header">Price</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <select name="min_price" class="c-select">
                <option value="">Min Price</option>
                <option value="5000" {{ request('min_price') == '5000' ? 'selected' : '' }}>$5,000</option>
                <option value="10000" {{ request('min_price') == '10000' ? 'selected' : '' }}>$10,000</option>
                <option value="20000" {{ request('min_price') == '20000' ? 'selected' : '' }}>$20,000</option>
                <option value="30000" {{ request('min_price') == '30000' ? 'selected' : '' }}>$30,000</option>
                <option value="50000" {{ request('min_price') == '50000' ? 'selected' : '' }}>$50,000</option>
            </select>
            <select name="max_price" class="c-select">
                <option value="">Max Price</option>
                <option value="10000" {{ request('max_price') == '10000' ? 'selected' : '' }}>$10,000</option>
                <option value="20000" {{ request('max_price') == '20000' ? 'selected' : '' }}>$20,000</option>
                <option value="30000" {{ request('max_price') == '30000' ? 'selected' : '' }}>$30,000</option>
                <option value="50000" {{ request('max_price') == '50000' ? 'selected' : '' }}>$50,000</option>
                <option value="75000" {{ request('max_price') == '75000' ? 'selected' : '' }}>$75,000</option>
                <option value="100000" {{ request('max_price') == '100000' ? 'selected' : '' }}>$100,000+</option>
            </select>
        </div>
    </div>

    <!-- Year -->
    <div class="filter-section">
        <h3 class="filter-header">Year</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <select name="min_year" class="c-select">
                <option value="">Min Year</option>
                @for($y=2026; $y>=2005; $y--)
                    <option value="{{ $y }}" {{ request('min_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="max_year" class="c-select">
                <option value="">Max Year</option>
                @for($y=2026; $y>=2005; $y--)
                    <option value="{{ $y }}" {{ request('max_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
    
    <!-- Mileage -->
    <div class="filter-section">
        <h3 class="filter-header">Mileage</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <select name="min_mileage" class="c-select">
                <option value="">Min</option>
                <option value="0" {{ request('min_mileage') == '0' ? 'selected' : '' }}>0</option>
                <option value="10000" {{ request('min_mileage') == '10000' ? 'selected' : '' }}>10,000</option>
                <option value="50000" {{ request('min_mileage') == '50000' ? 'selected' : '' }}>50,000</option>
                <option value="100000" {{ request('min_mileage') == '100000' ? 'selected' : '' }}>100,000</option>
            </select>
            <select name="max_mileage" class="c-select">
                <option value="">Max</option>
                <option value="10000" {{ request('max_mileage') == '10000' ? 'selected' : '' }}>10,000</option>
                <option value="30000" {{ request('max_mileage') == '30000' ? 'selected' : '' }}>30,000</option>
                <option value="60000" {{ request('max_mileage') == '60000' ? 'selected' : '' }}>60,000</option>
                <option value="100000" {{ request('max_mileage') == '100000' ? 'selected' : '' }}>100,000</option>
                <option value="150000" {{ request('max_mileage') == '150000' ? 'selected' : '' }}>150,000</option>
            </select>
        </div>
    </div>

    <!-- Fuel Type -->
    <div class="filter-section">
        <h3 class="filter-header">Fuel Type</h3>
            <div class="filter-radio-group">
            <label class="radio-item"><input type="checkbox" name="fuel_type[]" value="Gasoline" {{ in_array('Gasoline', (array)request('fuel_type')) ? 'checked' : '' }} onchange="this.form.submit()"> Gasoline</label>
            <label class="radio-item"><input type="checkbox" name="fuel_type[]" value="Hybrid" {{ in_array('Hybrid', (array)request('fuel_type')) ? 'checked' : '' }} onchange="this.form.submit()"> Hybrid</label>
            <label class="radio-item"><input type="checkbox" name="fuel_type[]" value="Electric" {{ in_array('Electric', (array)request('fuel_type')) ? 'checked' : '' }} onchange="this.form.submit()"> Electric</label>
            <label class="radio-item"><input type="checkbox" name="fuel_type[]" value="Diesel" {{ in_array('Diesel', (array)request('fuel_type')) ? 'checked' : '' }} onchange="this.form.submit()"> Diesel</label>
        </div>
    </div>

    <!-- Transmission -->
    <div class="filter-section">
        <h3 class="filter-header">Transmission</h3>
            <div class="filter-radio-group">
            <label class="radio-item"><input type="checkbox" name="transmission[]" value="Automatic" {{ in_array('Automatic', (array)request('transmission')) ? 'checked' : '' }} onchange="this.form.submit()"> Automatic</label>
            <label class="radio-item"><input type="checkbox" name="transmission[]" value="Manual" {{ in_array('Manual', (array)request('transmission')) ? 'checked' : '' }} onchange="this.form.submit()"> Manual</label>
            <label class="radio-item"><input type="checkbox" name="transmission[]" value="CVT" {{ in_array('CVT', (array)request('transmission')) ? 'checked' : '' }} onchange="this.form.submit()"> CVT</label>
        </div>
    </div>

    <!-- Drivetrain -->
    <div class="filter-section">
        <h3 class="filter-header">Drivetrain</h3>
        <div class="filter-radio-group">
            <label class="radio-item"><input type="checkbox" name="drivetrain[]" value="FWD" {{ in_array('FWD', (array)request('drivetrain')) ? 'checked' : '' }} onchange="this.form.submit()"> FWD</label>
            <label class="radio-item"><input type="checkbox" name="drivetrain[]" value="RWD" {{ in_array('RWD', (array)request('drivetrain')) ? 'checked' : '' }} onchange="this.form.submit()"> RWD</label>
            <label class="radio-item"><input type="checkbox" name="drivetrain[]" value="AWD" {{ in_array('AWD', (array)request('drivetrain')) ? 'checked' : '' }} onchange="this.form.submit()"> AWD</label>
            <label class="radio-item"><input type="checkbox" name="drivetrain[]" value="4WD" {{ in_array('4WD', (array)request('drivetrain')) ? 'checked' : '' }} onchange="this.form.submit()"> 4WD</label>
        </div>
    </div>

    <!-- City/Location -->
    <div class="filter-section">
        <h3 class="filter-header">City/Location</h3>
        <div class="mb-12">
            <select name="city" class="c-select" onchange="this.form.submit()">
                <option value="">All Cities</option>
                <optgroup label="🇰🇷 Korea">
                    <option value="Seoul" {{ request('city') == 'Seoul' ? 'selected' : '' }}>Seoul</option>
                    <option value="Busan" {{ request('city') == 'Busan' ? 'selected' : '' }}>Busan</option>
                    <option value="Incheon" {{ request('city') == 'Incheon' ? 'selected' : '' }}>Incheon</option>
                    <option value="Daegu" {{ request('city') == 'Daegu' ? 'selected' : '' }}>Daegu</option>
                    <option value="Daejeon" {{ request('city') == 'Daejeon' ? 'selected' : '' }}>Daejeon</option>
                    <option value="Gwangju" {{ request('city') == 'Gwangju' ? 'selected' : '' }}>Gwangju</option>
                    <option value="Ulsan" {{ request('city') == 'Ulsan' ? 'selected' : '' }}>Ulsan</option>
                    <option value="Suwon" {{ request('city') == 'Suwon' ? 'selected' : '' }}>Suwon</option>
                    <option value="Changwon" {{ request('city') == 'Changwon' ? 'selected' : '' }}>Changwon</option>
                    <option value="Goyang" {{ request('city') == 'Goyang' ? 'selected' : '' }}>Goyang</option>
                </optgroup>
                <optgroup label="🌍 Other">
                    <option value="Dubai" {{ request('city') == 'Dubai' ? 'selected' : '' }}>Dubai</option>
                    <option value="Riyadh" {{ request('city') == 'Riyadh' ? 'selected' : '' }}>Riyadh</option>
                    <option value="Jeddah" {{ request('city') == 'Jeddah' ? 'selected' : '' }}>Jeddah</option>
                </optgroup>
            </select>
        </div>
    </div>

    <!-- Export Feature -->

    <div class="filter-section">
        <h3 class="filter-header">Export</h3>
        <label class="radio-item">
            <input type="checkbox" name="is_exportable" value="1" {{ request('is_exportable') ? 'checked' : '' }} onchange="this.form.submit()">
            <b style="color: #6041E0;">Export Eligible Only</b>
        </label>
    </div>
    
    <!-- Exterior Color -->
        <div class="filter-section">
        <h3 class="filter-header">Exterior Color</h3>
            <div class="filter-radio-group">
            <label class="radio-item"><input type="checkbox" name="color[]" value="Black" {{ in_array('Black', (array)request('color')) ? 'checked' : '' }} onchange="this.form.submit()"> Black</label>
            <label class="radio-item"><input type="checkbox" name="color[]" value="White" {{ in_array('White', (array)request('color')) ? 'checked' : '' }} onchange="this.form.submit()"> White</label>
            <label class="radio-item"><input type="checkbox" name="color[]" value="Silver" {{ in_array('Silver', (array)request('color')) ? 'checked' : '' }} onchange="this.form.submit()"> Silver</label>
            <label class="radio-item"><input type="checkbox" name="color[]" value="Red" {{ in_array('Red', (array)request('color')) ? 'checked' : '' }} onchange="this.form.submit()"> Red</label>
            <label class="radio-item"><input type="checkbox" name="color[]" value="Blue" {{ in_array('Blue', (array)request('color')) ? 'checked' : '' }} onchange="this.form.submit()"> Blue</label>
        </div>
    </div>

    <!-- Search Button -->
    <div class="filter-section">
        <button type="submit" class="search-btn" style="width: 100%; padding: 12px; background: #6041E0; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
            <i class="fas fa-search" style="margin-right: 8px;"></i>Search Cars
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const makeSelect = document.querySelector('select[name="make"]');
    const modelSelect = document.querySelector('select[name="model"]');
    
    if (makeSelect && modelSelect) {
        // Function to load models for a make
        function loadModels(makeSlug) {
            if (makeSlug) {
                modelSelect.disabled = true;
                modelSelect.innerHTML = '<option value="">Loading...</option>';
                
                fetch(`/api/models/${makeSlug}`)
                    .then(response => response.json())
                    .then(models => {
                        modelSelect.innerHTML = '<option value="">All Models</option>';
                        models.forEach(m => {
                            const opt = document.createElement('option');
                            opt.value = m.slug || m.id;
                            opt.textContent = m.name;
                            modelSelect.appendChild(opt);
                        });
                        modelSelect.disabled = false;
                        
                        // If a model was previously selected, reselect it
                        const currentModel = '{{ request('model') }}';
                        if (currentModel) {
                            modelSelect.value = currentModel;
                        }
                    })
                    .catch(error => {
                        console.error('Error loading models:', error);
                        modelSelect.innerHTML = '<option value="">Error loading models</option>';
                    });
            } else {
                modelSelect.innerHTML = '<option value="">All Models</option>';
                modelSelect.disabled = true;
            }
        }
        
        // Load models if a make is already selected
        if (makeSelect.value) {
            loadModels(makeSelect.value);
        }
        
        // Listen for make changes
        makeSelect.addEventListener('change', function() {
            loadModels(this.value);
        });
    }
});
</script>
