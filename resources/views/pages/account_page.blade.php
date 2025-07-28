<x-app>
     <!-- Page Title -->
        <section class="s-page-title">
            <div class="container">
                <div class="content">
                    <h1 class="title-page">My Account</h1>
                    <ul class="breadcrumbs-page">
                        <li><a href="index.html" class="h6 link">Home</a></li>
                        <li class="d-flex"><i class="icon icon-caret-right"></i></li>
                        <li>
                            <h6 class="current-page fw-normal">My account</h6>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <!-- /Page Title -->
        <!-- Account -->
        <section class="flat-spacing">
            <input class="fileInputDash" type="file" accept="image/*" style="display: none;">
            <div class="container">
                <div class="row">
                    <div class="col-xl-3 d-none d-xl-block">
                        <div class="sidebar-account sidebar-content-wrap sticky-top">
                            <div class="account-author">
                                <div class="author_avatar">
                                    <div class="image">
                                        <img class="lazyload imgDash" src="images/avatar/avatar-4.jpg" data-src="images/avatar/avatar-4.jpg"
                                            alt="Avatar">
                                    </div>
                                    <div class="btn-change_img box-icon changeImgDash">
                                        <i class="icon icon-camera"></i>
                                    </div>
                                </div>
                                <h4 class="author_name">Themesflat</h4>
                                <p class="author_email h6">support@ochaka.com</p>
                            </div>
                            <ul class="my-account-nav">
                                <li>
                                    <p class="my-account-nav_item h5 active">
                                        <i class="icon icon-circle-four"></i>
                                        Dashboard
                                    </p>
                                </li>
                                <li>
                                    <a href="account-orders.html" class="my-account-nav_item h5">
                                        <i class="icon icon-box-arrow-down"></i>
                                        Oders
                                    </a>
                                </li>
                                <li>
                                    <a href="account-addresses.html" class="my-account-nav_item h5">
                                        <i class="icon icon-address-book"></i>
                                        My address
                                    </a>
                                </li>
                                <li>
                                    <a href="account-setting.html" class="my-account-nav_item h5">
                                        <i class="icon icon-setting"></i>
                                        Setting
                                    </a>
                                </li>
                                <li>
                                    <a href="index.html" class="my-account-nav_item h5">
                                        <i class="icon icon-sign-out"></i>
                                        Log out
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-9">
                        <div class="my-account-content">
                            <div class="acount-order_stats">
                                <div dir="ltr" class="swiper tf-swiper" data-preview="3" data-tablet="3" data-mobile-sm="2" data-mobile="1"
                                    data-space-lg="48" data-space-md="16" data-space="12" data-pagination="1" data-pagination-sm="2"
                                    data-pagination-md="3" data-pagination-lg="3">
                                    <div class="swiper-wrapper">
                                        <!-- item 1 -->
                                        <div class="swiper-slide">
                                            <div class="order-box">
                                                <div class="order_icon">
                                                    <i class="icon icon-package-thin"></i>
                                                </div>
                                                <div class="order_info">
                                                    <p class="info_label h6">Wait for confirmation</p>
                                                    <h2 class="info_count type-semibold">29</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- item 2 -->
                                        <div class="swiper-slide">
                                            <div class="order-box">
                                                <div class="order_icon">
                                                    <i class="icon icon-check-fat"></i>
                                                </div>
                                                <div class="order_info">
                                                    <p class="info_label h6">Successful order</p>
                                                    <h2 class="info_count type-semibold">35</h2>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- item 3 -->
                                        <div class="swiper-slide">
                                            <div class="order-box">
                                                <div class="order_icon">
                                                    <i class="icon icon-box-arrow-up"></i>
                                                </div>
                                                <div class="order_info">
                                                    <p class="info_label h6">Total order</p>
                                                    <h2 class="info_count type-semibold">108</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sw-dot-default tf-sw-pagination"></div>
                                </div>
                            </div>
                            <div class="account-my_order">
                                <h2 class="account-title type-semibold">Recent Orders</h2>
                                <div class="overflow-auto">
                                    <table class="table-my_order order_recent">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Products</th>
                                                <th>Pricing</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#12840629</td>
                                                <td>
                                                    <div class="tb-order_product">
                                                        <a href="product-detail.html" class="img-prd">
                                                            <img class="lazyload" src="images/products/product-10.jpg"
                                                                data-src="images/products/product-10.jpg" alt="T Shirt">
                                                        </a>
                                                        <div class="infor-prd">
                                                            <h6>
                                                                <a href="product-detail.html" class="prd_name link">
                                                                    Short Sleeve Office Shirt
                                                                </a>
                                                            </h6>
                                                            <p class="prd_select text-small">
                                                                Clothing <span>Size: XS</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="tb-order_price">$22.99</td>
                                                <td>
                                                    <div class="tb-order_status stt-complete">
                                                        Completed
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#12870127</td>
                                                <td>
                                                    <div class="tb-order_product">
                                                        <a href="product-detail.html" class="img-prd">
                                                            <img class="lazyload" src="images/products/product-33.jpg"
                                                                data-src="images/products/product-33.jpg" alt="T Shirt">
                                                        </a>
                                                        <div class="infor-prd">
                                                            <h6>
                                                                <a href="product-detail.html" class="prd_name link">
                                                                    Nike Sportswear Tee Shirts
                                                                </a>
                                                            </h6>
                                                            <p class="prd_select text-small">
                                                                Clothing <span>Size: L</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="tb-order_price">$55.89</td>
                                                <td>
                                                    <div class="tb-order_status stt-pending">
                                                        Pending
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#12870345</td>
                                                <td>
                                                    <div class="tb-order_product">
                                                        <a href="product-detail.html" class="img-prd">
                                                            <img class="lazyload" src="images/products/product-36.jpg"
                                                                data-src="images/products/product-36.jpg" alt="T Shirt">
                                                        </a>
                                                        <div class="infor-prd">
                                                            <h6>
                                                                <a href="product-detail.html" class="prd_name link">
                                                                    Women's straight leg pants
                                                                </a>
                                                            </h6>
                                                            <p class="prd_select text-small">
                                                                Clothing <span>Size: XL</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="tb-order_price">$25.79</td>
                                                <td>
                                                    <div class="tb-order_status stt-delivery">
                                                        Delivery
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#12870789</td>
                                                <td>
                                                    <div class="tb-order_product">
                                                        <a href="product-detail.html" class="img-prd">
                                                            <img class="lazyload" src="images/products/underwear/product-15.jpg"
                                                                data-src="images/products/underwear/product-15.jpg" alt="Bikini">
                                                        </a>
                                                        <div class="infor-prd">
                                                            <h6>
                                                                <a href="product-detail.html" class="prd_name link">
                                                                    Short sleeve office shirt
                                                                </a>
                                                            </h6>
                                                            <p class="prd_select text-small">
                                                                Clothing <span>Size: M</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="tb-order_price">$99.99</td>
                                                <td>
                                                    <div class="tb-order_status stt-cancel">
                                                        Canceled
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#12870808</td>
                                                <td>
                                                    <div class="tb-order_product">
                                                        <a href="product-detail.html" class="img-prd">
                                                            <img class="lazyload" src="images/products/product-55.jpg"
                                                                data-src="images/products/product-55.jpg" alt="T Shirt">
                                                        </a>
                                                        <div class="infor-prd">
                                                            <h6>
                                                                <a href="product-detail.html" class="prd_name link">
                                                                    Loose V-neck T-shirt
                                                                </a>
                                                            </h6>
                                                            <p class="prd_select text-small">
                                                                Clothing <span>Size: XL</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="tb-order_price">$52.99</td>
                                                <td>
                                                    <div class="tb-order_status stt-complete">
                                                        Completed
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr class="tb-order-item">
                                                <td class="tb-order_code">#12870231</td>
                                                <td>
                                                    <div class="tb-order_product">
                                                        <a href="product-detail.html" class="img-prd">
                                                            <img class="lazyload" src="images/products/underwear/product-50.jpg"
                                                                data-src="images/products/underwear/product-50.jpg" alt="Bikini">
                                                        </a>
                                                        <div class="infor-prd">
                                                            <h6>
                                                                <a href="product-detail.html" class="prd_name link">
                                                                    Fashionable workout tops
                                                                </a>
                                                            </h6>
                                                            <p class="prd_select text-small">
                                                                Clothing <span>Size: XS</span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="tb-order_price">$89.99</td>
                                                <td>
                                                    <div class="tb-order_status stt-cancel">
                                                        Canceled
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="wd-full wg-pagination">
                                    <a href="#" class="pagination-item h6 direct"><i class="icon icon-caret-left"></i></a>
                                    <a href="#" class="pagination-item h6">1</a>
                                    <span class="pagination-item h6 active">2</span>
                                    <a href="#" class="pagination-item h6">3</a>
                                    <a href="#" class="pagination-item h6">4</a>
                                    <a href="#" class="pagination-item h6">5</a>
                                    <a href="#" class="pagination-item h6 direct"><i class="icon icon-caret-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /Account -->
</x-app>