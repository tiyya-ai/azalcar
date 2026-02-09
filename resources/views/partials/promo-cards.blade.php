<section class="py-12 bg-white">
    <div class="container mx-auto px-4 md:px-12">
        <!-- Single Large Promo Card (News) -->
        <div class="bg-white rounded-[1rem] border border-[#E0E4E3] overflow-hidden flex flex-col md:flex-row items-center w-full min-h-[280px] shadow-sm hover:shadow-md transition-shadow duration-300">
            <!-- Text Content -->
            <div class="p-8 md:p-12 flex-1 z-10">
                <span class="block text-[14px] font-[700] text-[#141817] mb-3">azalcars news</span>
                <h2 class="text-[26px] font-[700] text-[#141817] mb-4 leading-tight">
                    Latest Global Car Trends
                </h2>
                <p class="text-[#4b5563] text-[17px] mb-8 leading-relaxed max-w-[500px]">
                    We analyze the global market to determine which new cars offer the best value.
                </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('news.index') }}" class="inline-block bg-white text-[#141817] border border-[#141817] font-[700] py-3.5 px-8 rounded-full hover:bg-gray-50 transition-colors text-center text-[16px]">
                            View All News
                        </a>
                        <a href="{{ route('listings.search') }}" class="inline-block bg-[#141817] text-white font-[700] py-3.5 px-8 rounded-full hover:bg-gray-800 transition-colors text-center text-[16px]">
                            Shop all cars
                        </a>
                    </div>
            </div>
            
            <!-- Illustration Area -->
            <div class="relative w-full md:w-1/2 h-[300px] md:h-full self-stretch overflow-hidden bg-[#F7F2FF] md:bg-transparent">
                <img
                    src="{{ asset('assets/images/AMI25.webp') }}"
                    alt="Global Car Trends"
                    class="absolute bottom-0 right-0 w-full h-auto max-w-[600px] object-contain object-bottom transform translate-x-4 h-full"
                >
            </div>
        </div>
    </div>
</section>
