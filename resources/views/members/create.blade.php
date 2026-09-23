<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <span class="px-3 py-1 text-xs font-bold text-green-700 uppercase bg-green-100 rounded-full">Membership</span>
            <h2 class="text-xl font-bold leading-tight text-gray-800">Comprehensive Registration & Agri-Resume</h2>
        </div>
    </x-slot>

    {{-- Form State Container --}}
    <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data"
        x-data="{ 
            selectedRegion: '{{ $userRegionId ?? '' }}', 
            regions: {{ $regions->toJson() }},
            specialization: '',
            hvcdpCategory: '',
            memberType: '',
            otherMemberType: '',
            services: [],
            trainingType: 'None',
            otherTraining: '',
            ownershipType: '',
            
            init() {
                if(this.selectedRegion) {
                    this.$nextTick(() => {
                        console.log('Region auto-selected: ' + this.selectedRegion);
                    });
                }
            },

            get filteredProvinces() {
                if (!this.selectedRegion) return [];
                let region = this.regions.find(r => r.id == this.selectedRegion);
                return region ? region.provinces : [];
            },
            get filteredOrganizations() {
                if (!this.selectedRegion) return [];
                let region = this.regions.find(r => r.id == this.selectedRegion);
                return region ? region.organizations : [];
            },
            toggleService(val) {
                if (val === 'None') {
                    this.services = ['None'];
                } else {
                    this.services = this.services.filter(s => s !== 'None');
                    if (this.services.includes(val)) {
                        this.services = this.services.filter(s => s !== val);
                    } else {
                        this.services.push(val);
                    }
                }
            }
        }" class="max-w-6xl px-4 pb-20 mx-auto mt-8">
        @csrf

        {{-- Success and Error Alerts --}}
        <div class="max-w-6xl px-4 mx-auto mt-6">
            @if (session('success'))
                <div x-data="{ show: true }" 
                    x-show="show" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="flex items-center justify-between p-4 mb-4 border-l-4 border-green-500 shadow-sm bg-green-50 rounded-xl">
                    <div class="flex items-center">
                        <i class="mr-3 text-green-500 fas fa-check-circle"></i>
                        <p class="text-sm font-bold text-green-800">{{ session('success') }}</p>
                    </div>
                    <button type="button" @click="show = false" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" 
                    x-show="show" 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    class="p-4 mb-4 border-l-4 border-red-500 shadow-sm bg-red-50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center">
                            <i class="mr-3 text-red-500 fas fa-exclamation-triangle"></i>
                            <p class="text-sm font-black text-red-800 uppercase">Registration Failed</p>
                        </div>
                        <button type="button" @click="show = false" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <ul class="ml-8 list-disc">
                        @foreach ($errors->all() as $error)
                            <li class="text-xs font-medium text-red-700">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Section 1: Identity & Demographics --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 mb-8 overflow-hidden">
            <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-black text-green-600 uppercase tracking-[0.2em]">I. Identity & Demographics</h3>
                <span class="text-[10px] text-gray-400 font-medium">Step 1 of 4</span>
            </div>
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Last Name</label>
                        <input type="text" name="last_name" required value="{{ old('last_name') }}" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">First Name</label>
                        <input type="text" name="first_name" required value="{{ old('first_name') }}" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Middle Name</label>
                        <input type="text" name="middle_name" value="{{ old('middle_name') }}" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Suffix</label>
                        <select name="suffix" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                            <option value="">None</option>
                            <option value="Jr.">Jr.</option>
                            <option value="Sr.">Sr.</option>
                            <option value="III">III</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Sex</label>
                        <select name="sex" required class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Civil Status</label>
                        <select name="civil_status" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                            <option value="Single">Single</option>
                        </select>
                    </div>
                    @php
                        $maxDate = date('Y-m-d', strtotime('-10 years'));
                        $minDate = date('Y-m-d', strtotime('-80 years'));
                    @endphp

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Date of Birth</label>
                        <input type="date" name="dob" min="{{ $minDate }}" max="{{ $maxDate }}" required class="w-full border-gray-200 rounded-2xl focus:ring-green-500 @error('dob') border-red-500 @enderror">
                        @error('dob')
                            <p class="text-red-500 text-[10px] mt-1 ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Contact Number</label>
                        <input type="text" name="contact_no" required value="{{ old('contact_no') }}" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                </div>
                
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Email (Unique ID)</label>
                    <input type="email" name="email" required value="{{ old('email') }}" class="w-full border-gray-200 rounded-2xl focus:ring-green-500" placeholder="email@example.com">
                </div>
            </div>
        </div>

        {{-- Section 2: Address & Location Details --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 mb-8 overflow-hidden">
            <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-black text-green-600 uppercase tracking-[0.2em]">II. Address & Location Details</h3>
                <span class="text-[10px] text-gray-400 font-medium">Step 2 of 4</span>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Region</label>
                        <select x-model="selectedRegion" name="region_id" required class="w-full border-gray-200 rounded-2xl focus:ring-green-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                            <option value="">Select Region</option>
                            <template x-for="region in regions" :key="region.id">
                                <option :value="region.id" x-text="region.name" :selected="region.id == selectedRegion"></option>
                            </template>
                        </select>
                        @if($userRegionId)
                            <p class="mt-1 ml-1 text-[9px] text-emerald-600 font-bold uppercase italic">* Locked to your assigned jurisdiction</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Province</label>
                        <select name="province_id" required class="w-full border-gray-200 rounded-2xl focus:ring-green-500" :disabled="!selectedRegion">
                            <option value="">Select Province</option>
                            <template x-for="province in filteredProvinces" :key="province.id">
                                <option :value="province.id" x-text="province.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">City / Municipality</label>
                        <input type="text" name="city_municipality" required class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">District</label>
                        <input type="text" name="district" placeholder="District I" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Barangay</label>
                        <input type="text" name="barangay" required class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Zip Code</label>
                        <input type="text" name="zip_code" maxlength="4" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Professional & Program Details --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 mb-8 overflow-hidden">
            <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-black text-green-600 uppercase tracking-[0.2em]">III. Professional & Program Details</h3>
                <span class="text-[10px] text-gray-400 font-medium">Step 3 of 4</span>
            </div>

            <div class="p-8 space-y-10">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Member Type</label>
                        <select x-model="memberType" name="member_type_select" class="w-full transition-all border-gray-200 rounded-2xl bg-gray-50 focus:ring-green-500">
                            <option value="">Select Type</option>
                            <option value="Farmer">Farmer</option>
                            <option value="Fisher">Fisher</option>
                            <option value="Student">Student</option>
                            <option value="AEW">Agricultural Extension Worker (AEW)</option>
                            <option value="Others">Others</option>
                        </select>
                        <div x-show="memberType === 'Others'" x-transition class="mt-2">
                            <input type="text" x-model="otherMemberType" placeholder="Specify type..." class="w-full text-sm italic border-green-200 rounded-xl bg-green-50/30">
                        </div>
                        <input type="hidden" name="member_type" :value="memberType === 'Others' ? otherMemberType : memberType">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Occupation</label>
                        <input type="text" name="occupation" class="w-full border-gray-200 rounded-2xl focus:ring-green-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Organization</label>
                        <select name="organization_id" class="w-full bg-white border-gray-200 rounded-2xl" :disabled="!selectedRegion">
                            <option value="">Select Organization</option>
                            <template x-for="org in filteredOrganizations" :key="org.id">
                                <option :value="org.id" x-text="org.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Specialization Section --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-4 ml-1">As a 4-H member, which of the following is your specialization?</label>
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-4">
                                <select name="specialization" x-model="specialization" required class="w-full border-gray-200 bg-gray-50 rounded-2xl focus:ring-green-500">
                                    <option value="">Select Specialization</option>
                                    <option value="HVCDP">High Value Crops (HVCDP)</option>
                                    <option value="Livestock">Livestock</option>
                                    <option value="Fisheries">Fisheries</option>
                                    <option value="Rice">Rice</option>
                                    <option value="Corn">Corn</option>
                                    <option value="Poultry">Poultry</option>
                                    <option value="Combination">Combination of Multiple Fields</option>
                                </select>
                
                                <div x-show="specialization === 'HVCDP' || specialization === 'Combination'" x-transition x-cloak>
                                    <label class="block text-[10px] font-bold text-green-700 uppercase mb-2 ml-1">HVCDP Category</label>
                                    <select name="hvcdp_category" x-model="hvcdpCategory" class="w-full border-gray-200 rounded-xl bg-green-50/30">
                                        <option value="">Select Category</option>
                                        <option value="Vegetables">Vegetables</option>
                                        <option value="Fruits">Fruits</option>
                                        <option value="Industrial Crops">Industrial Crops</option>
                                        <option value="Alternative Staple">Alternative Staple Food</option>
                                        <option value="Cut Across">Cut Across (Multiple)</option>
                                    </select>
                                </div>
                            </div>
                
                            <div class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100 min-h-[100px]">
                                <div x-show="hvcdpCategory === 'Vegetables' || hvcdpCategory === 'Cut Across'" x-transition>
                                    <h4 class="text-[10px] font-black text-gray-500 uppercase mb-3">Vegetables & Planting Materials</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['Mungbean', 'Peanut', 'White Potato', 'Hot Pepper', 'Garlic', 'Shallot', 'Red Onion', 'Ginger', 'Lowland Vegetables', 'Highland Vegetables', 'Malunggay', 'Soybean', 'Adlai', 'Mushroom', 'Bamboo', 'Vanilla', 'Herbs and Spices'] as $veg)
                                            <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-green-400 transition">
                                                <input type="checkbox" name="crops[]" value="{{ $veg }}" class="mr-2 text-green-600 rounded size-3">
                                                {{ $veg }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                
                                <div x-show="hvcdpCategory === 'Fruits' || hvcdpCategory === 'Cut Across'" x-transition class="mt-4">
                                    <h4 class="text-[10px] font-black text-gray-500 uppercase mb-3">Fruits</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['Mango', 'Banana', 'Pineapple', 'Durian', 'Cashew', 'Pili', 'Citrus', 'Strawberry', 'Guyabano', 'Mangosteen', 'Lanzones', 'Rambutan', 'Dragon Fruit', 'Melon/Watermelon'] as $fruit)
                                            <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-orange-400 transition">
                                                <input type="checkbox" name="crops[]" value="{{ $fruit }}" class="mr-2 text-orange-600 rounded size-3">
                                                {{ $fruit }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                
                                <div x-show="hvcdpCategory === 'Industrial Crops' || hvcdpCategory === 'Cut Across'" x-transition class="mt-4">
                                    <h4 class="text-[10px] font-black text-gray-500 uppercase mb-3">Industrial Crops</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['Coffee', 'Cacao', 'Rubber'] as $ind)
                                            <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-brown-400 transition">
                                                <input type="checkbox" name="crops[]" value="{{ $ind }}" class="mr-2 text-amber-800 rounded size-3">
                                                {{ $ind }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                
                                <div x-show="hvcdpCategory === 'Alternative Staple' || hvcdpCategory === 'Cut Across'" x-transition class="mt-4">
                                    <h4 class="text-[10px] font-black text-gray-500 uppercase mb-3">Alternative Staple</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach(['Saba-Banana', 'Sweet Potato', 'Yam', 'Gabi', 'Arrowroot'] as $staple)
                                            <label class="flex items-center px-3 py-1 bg-white border border-gray-200 rounded-lg text-[11px] cursor-pointer hover:border-blue-400 transition">
                                                <input type="checkbox" name="crops[]" value="{{ $staple }}" class="mr-2 text-blue-600 rounded size-3">
                                                {{ $staple }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                
                                <div x-show="!hvcdpCategory" class="flex items-center justify-center h-full text-xs italic text-gray-400">
                                    Please select a category to view specific crops.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- ATI Services Section --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-4 ml-1">ATI Services Availed</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach(['LSA', 'Internship', 'Scholarship', 'Training', 'None'] as $service)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="services[]" value="{{ $service }}" class="hidden" @click="toggleService('{{ $service }}')" :checked="services.includes('{{ $service }}')">
                                    <span :class="services.includes('{{ $service }}') ? 'bg-green-600 border-green-600 text-white shadow-md' : 'bg-white border-gray-200 text-gray-600 hover:border-green-400'" class="block px-4 py-2 transition-all border rounded-xl">
                                        {{ $service }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Dynamic Service Detail Panels --}}
                    <div class="grid grid-cols-1 gap-6 mt-6 md:grid-cols-2">
                        <div x-show="services.includes('Internship')" x-transition x-cloak class="p-6 border border-purple-100 bg-purple-50/50 rounded-2xl">
                            <h4 class="text-[10px] font-black text-purple-700 uppercase mb-4">Internship Program Details</h4>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Select Program</label>
                                <select name="internship" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-purple-500">
                                    <option value="N/A">N/A</option>
                                    <option value="YFFLTPJ">Young Filipino Farm Leaders Training Program in Japan (YFFLTPJ)</option>
                                    <option value="FYFIPT">Filipino Young Farmers Internship Program in Taiwan (FYFIPT)</option>
                                </select>
                            </div>
                        </div>
                    
                        <div x-show="services.includes('Scholarship')" x-transition x-cloak class="p-6 border border-indigo-100 bg-indigo-50/50 rounded-2xl">
                            <h4 class="text-[10px] font-black text-indigo-700 uppercase mb-4">Scholarship Program Details</h4>
                            <div class="space-y-2">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Select Scholarship</label>
                                <select name="scholarship" class="w-full text-sm bg-white border-gray-200 rounded-xl focus:ring-indigo-500">
                                    <option value="None">None</option>
                                    <option value="EAsY Agri">Educational Assistance for the Youth: Degree Courses in Agriculture (EAsY Agri)</option>
                                    <option value="EdGE">Educational Grants for Extension Workers (EdGE)</option>
                                </select>
                            </div>
                        </div>
                    
                        <div x-show="services.includes('LSA')" x-transition x-cloak class="p-6 border border-blue-100 bg-blue-50/50 rounded-2xl">
                            <h4 class="text-[10px] font-black text-blue-700 uppercase mb-4">LSA Specifics</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Level</label>
                                    <select name="lsa_level" class="w-full text-sm border-gray-200 rounded-xl">
                                        <option value="">Select...</option>
                                        <option value="LSA I">LSA I</option>
                                        <option value="LSA II">LSA II</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Type</label>
                                    <select name="lsa_type" class="w-full text-sm border-gray-200 rounded-xl">
                                        <option value="">Select...</option>
                                        <option value="RCEF-LSA">RCEF-LSA</option>
                                        <option value="COCO-LSA">COCO-LSA</option>
                                        <option value="Regular">Regular LSA</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    
                        <div x-show="services.includes('Training')" x-transition x-cloak class="p-6 border border-orange-100 bg-orange-50/50 rounded-2xl">
                            <h4 class="text-[10px] font-black text-orange-700 uppercase mb-4">Latest Training Course</h4>
                            <select x-model="trainingType" class="w-full mb-3 text-sm border-gray-200 rounded-xl">
                                <option value="None">Select Course</option>
                                <option value="Crop Production">Crop Production</option>
                                <option value="Livestock">Livestock Management</option>
                                <option value="Others">Others (Specify)</option>
                            </select>
                            <div x-show="trainingType === 'Others'" x-transition>
                                <input type="text" name="other_training" x-model="otherTraining" placeholder="Name of training..." class="w-full text-sm italic bg-white border-orange-200 rounded-xl">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Agri-Resume & Field Experience Details --}}
        <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 mb-8 overflow-hidden">
            <div class="flex items-center justify-between px-8 py-4 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-xs font-black text-green-600 uppercase tracking-[0.2em]">IV. Agri-Resume & Field Experience Details</h3>
                <span class="text-[10px] text-gray-400 font-medium">Step 4 of 4</span>
            </div>

            <div class="p-8 space-y-8">
                {{-- Educational Background --}}
                <div>
                    <h4 class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">A. Educational Background</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Highest Attainment</label>
                            <select name="highest_education" class="w-full border-gray-200 rounded-2xl focus:ring-green-500 text-sm">
                                <option value="">Select Level</option>
                                <option value="High School">High School Student / Graduate</option>
                                <option value="Vocational">Vocational / Technical</option>
                                <option value="College Undergraduate">College Undergraduate</option>
                                <option value="College Graduate">College Graduate</option>
                                <option value="Post-Graduate">Post-Graduate</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Degree / Course (if applicable)</label>
                            <input type="text" name="degree_course" placeholder="e.g. BS Agriculture, BS Agribusiness" class="w-full border-gray-200 rounded-2xl focus:ring-green-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">School / Institution</label>
                            <input type="text" name="school_name" placeholder="Name of university or school" class="w-full border-gray-200 rounded-2xl focus:ring-green-500 text-sm">
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Farm Profile & Operational Scale --}}
                <div x-data="{ 
                    isRsbsa: '{{ old('is_rsbsa_registered', '0') }}' === '1'
                }">
                    <h4 class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">B. Farm Profile & Scale of Operation</h4>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        
                        {{-- Land Ownership --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Land Tenure / Ownership</label>
                            <select name="land_ownership" x-model="ownershipType" class="w-full text-sm border-gray-200 rounded-2xl focus:ring-green-500">
                                <option value="">Select Ownership</option>
                                <option value="Owner" {{ old('land_ownership') == 'Owner' ? 'selected' : '' }}>Owner / Titled</option>
                                <option value="Tenant" {{ old('land_ownership') == 'Tenant' ? 'selected' : '' }}>Tenant</option>
                                <option value="Lessee" {{ old('land_ownership') == 'Lessee' ? 'selected' : '' }}>Lessee / Renter</option>
                                <option value="Co-Owner" {{ old('land_ownership') == 'Co-Owner' ? 'selected' : '' }}>Family Co-Owner</option>
                            </select>
                            @error('land_ownership')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Farm Area --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Total Farm Area (Hectares / SqM)</label>
                            <input type="text" name="farm_area" value="{{ old('farm_area') }}" placeholder="e.g. 1.5 Ha or 500 SqM" class="w-full text-sm border-gray-200 rounded-2xl focus:ring-green-500">
                            @error('farm_area')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- RSBSA Registered Toggle --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">RSBSA Registered?</label>
                            <select name="is_rsbsa_registered" 
                                    x-on:change="isRsbsa = ($el.value === '1')" 
                                    class="w-full text-sm border-gray-200 rounded-2xl focus:ring-green-500">
                                <option value="0" {{ old('is_rsbsa_registered', '0') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_rsbsa_registered') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                            @error('is_rsbsa_registered')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- RSBSA Number (Conditional Display) --}}
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1" 
                                :class="{ 'text-gray-400': !isRsbsa, 'text-gray-700': isRsbsa }">
                                RSBSA Number <span x-show="isRsbsa" class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                name="rsbsa_no" 
                                value="{{ old('rsbsa_no') }}" 
                                placeholder="00-00-00-000-000000" 
                                :disabled="!isRsbsa"
                                :required="isRsbsa"
                                :class="{ 'bg-gray-100 cursor-not-allowed': !isRsbsa, 'bg-white': isRsbsa }"
                                class="w-full text-sm border-gray-200 rounded-2xl focus:ring-green-500 transition-colors duration-150">
                            @error('rsbsa_no')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>
                <hr class="border-gray-100">

                {{-- Key Farm Machinery & Innovations --}}
                <div>
                    <h4 class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">C. Machinery, Skills & Agro-Practices</h4>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Farm Equipment / Technologies Managed</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Tractor / Hand Tractor', 'Drip Irrigation', 'Greenhouse / Hydroponics', 'Drone / Precision Ag', 'Solar Pump', 'Processing Machinery', 'Livestock Feed Mill'] as $equip)
                                    <label class="flex items-center px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-xl text-xs cursor-pointer hover:border-green-400 transition">
                                        <input type="checkbox" name="farm_equipment[]" value="{{ $equip }}" class="mr-2 text-green-600 rounded size-3">
                                        {{ $equip }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Specialized Agricultural Skills</label>
                            <textarea name="agri_skills" rows="3" placeholder="e.g. Grafting, Insemination, Organic Fertilizer Preparation, Financial Record Keeping..." class="w-full border-gray-200 rounded-2xl focus:ring-green-500 text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Certifications, Achievements & Resume Attachments --}}
                <div>
                    <h4 class="text-[11px] font-black text-gray-700 uppercase tracking-wider mb-4">D. Certifications & Supporting Documents</h4>
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">NC Certifications (e.g., TESDA NC II Organic Agriculture)</label>
                            <input type="text" name="certifications" placeholder="e.g. Agricultural Crops Production NC II, Animal Production NC II" class="w-full border-gray-200 rounded-2xl focus:ring-green-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 ml-1">Upload Agri-Resume / Curriculum Vitae (PDF/Docx)</label>
                            <input type="file" name="agri_resume_file" accept=".pdf,.doc,.docx" class="w-full text-xs text-gray-500 border border-gray-200 rounded-2xl file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Final Submission Section --}}
        <div class="flex flex-col items-center justify-center mt-12 mb-24 space-y-6">
            <div class="w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent"></div>
            
            <div class="flex items-center space-x-4">
                <button type="reset" class="px-8 py-3 text-xs font-bold text-gray-500 uppercase bg-gray-100 rounded-2xl hover:bg-gray-200 transition-all">
                    Reset Form
                </button>
                <button type="submit" class="px-10 py-3 text-xs font-bold text-white uppercase bg-green-600 rounded-2xl shadow-lg hover:bg-green-700 hover:shadow-green-200 transition-all">
                    Submit Registration & Resume
                </button>
            </div>
        </div>
    </form>
</x-app-layout>