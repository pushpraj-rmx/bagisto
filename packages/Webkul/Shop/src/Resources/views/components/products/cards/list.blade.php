<v-product-list
    {{ $attributes }}
    :product="product"
>
</v-product-list>

@pushOnce('scripts')
    <script type="text/x-template" id="v-product-list-template">
        <div class="flex gap-[20px] rounded-[12px] p-[15px] max-sm:flex-wrap {{ $attributes["class"] }}">
            <div class="relative overflow-hidden  group max-w-[291px] max-h-[300px]">
                <a :href="`{{ route('shop.productOrCategory.index', '') }}/${product.url_key}`">
                    <img
                        class="rounded-sm bg-[#F5F5F5] group-hover:scale-105 transition-all duration-300"
                        :src="product.base_image.medium_image_url"
                        width="258"
                        height="250"
                    >
                </a>   
                 
                <div class="action-items bg-black">
                    <p
                        class="rounded-[44px] text-[#fff] text-[14px] px-[10px] bg-navyBlue inline-block absolute top-[20px] left-[20px]"
                        v-if="product.is_new"
                    >
                        {{-- @translations --}}
                        @lang('New')
                    </p>

                    <p
                        class="rounded-[44px] text-[#fff] text-[14px] px-[10px] bg-red-700 inline-block absolute top-[20px] left-[20px]"
                        v-if="product.on_sale"
                    >
                        {{-- @translations --}}
                        @lang('Sale')
                    </p>

                    <div class="group-hover:bottom-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <a
                            class="flex justify-center items-center w-[30px] h-[30px] bg-white rounded-md cursor-pointer absolute top-[20px] right-[20px] icon-heart text-[25px]"
                            @click="addToWishlist()"
                        >
                        </a>

                        <a
                            class="flex justify-center items-center w-[30px] h-[30px] bg-white rounded-md cursor-pointer absolute top-[60px] right-[20px] icon-compare text-[25px]"
                            @click="addToCompare(product.id)"
                        >
                        </a>
                    </div>
                </div>
            </div>

            <div class="">
                <div class="flex justify-between">
                    <p class="text-base"  v-text="product.name"></p>
                </div>

                <div class="flex gap-2.5 text-lg" v-html="product.price_html"></div>

                <div class="flex gap-4 mt-[8px]">
                    <span class="rounded-full w-[30px] h-[30px] block cursor-pointer bg-[#B5DCB4]"></span>

                    <span class="rounded-full w-[30px] h-[30px] block cursor-pointer bg-[#5C5C5C]"></span>
                </div>
                
                <button
                    class="mt-[20px] ml-[0px] block mx-auto w-full bg-navyBlue text-white text-[16px] max-w-[374px] font-medium py-[16px] px-[43px] rounded-[18px] text-center"
                    @click="addToCart()"
                >
                    @lang('shop::app.components.products.add-to-cart')
                 </button>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-product-list', {
            template: '#v-product-list-template',

            props: ['product'],

            data() {
                return {
                    isImageLoading: true,

                    isCustomer: '{{ auth()->guard('customer')->check() }}',
                }
            },

            methods: {
                addToWishlist() {
                    this.$axios.post(`{{ route('shop.api.customers.account.wishlist.store') }}`, {
                            product_id: this.product.id
                        })
                        .then(response => {
                            alert(response.data.data.message);
                        })
                        .catch(error => {});
                },

                addToCompare(productId) {
                    /**
                     * This will handle for customers.
                     */
                    if (this.isCustomer) {
                        this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                'product_id': productId
                            })
                            .then(response => {
                                alert(response.data.data.message);
                            })
                            .catch(error => {});

                        return;
                    }

                    /**
                     * This will handle for guests.
                     */
                    let existingItems = this.getStorageValue(this.getCompareItemsStorageKey()) ?? [];

                    if (existingItems.length) {
                        if (! existingItems.includes(productId)) {
                            existingItems.push(productId);

                            this.setStorageValue(this.getCompareItemsStorageKey(), existingItems);

                            alert('Added product in compare.');
                        } else {
                            alert('Product is already added in compare.');
                        }
                    } else {
                        this.setStorageValue(this.getCompareItemsStorageKey(), [productId]);

                        alert('Added product in compare.');
                    }
                },

                getCompareItemsStorageKey() {
                    return 'compare_items';
                },

                setStorageValue(key, value) {
                    window.localStorage.setItem(key, JSON.stringify(value));
                },

                getStorageValue(key) {
                    let value = window.localStorage.getItem(key);

                    if (value) {
                        value = JSON.parse(value);
                    }

                    return value;
                },

                addToCart() {
                    this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', {
                            'quantity': 1,
                            'product_id': this.product.id,
                        })
                        .then(response => {
                            alert(response.data.message);
                        })
                        .catch(error => {});
                },
            },
        });
    </script>
@endpushOnce
