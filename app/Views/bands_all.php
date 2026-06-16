<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<section class="bands-page">

    <div class="container">

        <!-- Breadcrumb -->

        <div class="search-breadcrumb">

            <a href="<?= base_url(); ?>">
                Home
            </a>

            <span>/</span>

            <span>
                All Brands
            </span>

        </div>


        <!-- Header -->

        <div class="bands-header">

            <div>
                <h1>
                    All Brands
                </h1>

                <div class="bands-count">
                    <?= count($brands); ?> Brands
                </div>

            </div>

            <div class="bands-search-wrap">
                <input
                    type="text"
                    id="brandSearch"
                    class="bands-search"
                    placeholder="Search Brands" >
                <div
                    id="brandSuggestions"
                    class="brand-suggestions"
                ></div>
            </div>

        </div>
        
        
        <?php
            $perPage=24;
            $totalBrands=count($brands);
            $totalPages=ceil($totalBrands/$perPage);
            ?>

            <!-- Grid -->

            <div class="bands-grid" id="bandsGrid">
                <?php foreach($brands as $brand){ ?>
                    <a href="#" class="band-card brand-item" >
                        <div class="band-image">
                            <img
                                src="<?= !empty($brand['brand_img'])
                                            ? $brand['brand_img']
                                            : base_url('Images/icons/ic_brand.png'); ?>"
                                onerror="this.src='<?= base_url('Images/icons/ic_brand.png'); ?>'"
                            >
                        </div>
                        <div class="band-name">
                            <?= $brand['brand_name']; ?>
                        </div>
                    </a>
                <?php } ?>
            </div>


            <!-- Pagination -->

            <div class="brands-pagination">
                <button
                    id="brandPrev"
                    class="page-btn"
                >
                    <i class="fa fa-chevron-left"></i>
                </button>

                <?php for($i=1;$i<=$totalPages;$i++){ ?>
                    <button
                        class=" page-btn  brand-page-btn

                            <?= ($i==1)
                                ? 'active'
                                : ''; ?> "
                        data-page="<?= $i; ?>" >
                        <?= $i; ?>
                    </button>

                <?php } ?>
                
                <button
                    id="brandNext"
                    class="page-btn" >
                    <i class="fa fa-chevron-right"></i>
                </button>
            </div>
    </div>
    
    
    <script>

        const brands = [

            <?php foreach($brands as $brand){ ?>
            {
                id:'<?= $brand['id']; ?>',
                name:'<?= addslashes($brand['brand_name']); ?>'
            },
            <?php } ?>

        ];



        $("#brandSearch").on(
            "keyup",
            function(){

                let search= $(this)  .val() .toLowerCase();
                let html='';

                if(search.length<1){
                    $("#brandSuggestions")
                    .hide();

                    return;
                }

                let results=  brands.filter(  b=>  b.name .toLowerCase().includes(search) ) .slice(0,4);
                
                results.forEach(item=>{
                            html+=`
                            <a
                                href="#"
                                class="brand-suggestion"
                            >
                                ${item.name}
                            </a>
                            `;
                        });

                        $("#brandSuggestions")
                        .html(html)
                        .show();
            }
        );


        $(document).click(function(e){
            if(!$(e.target).closest(".bands-search-wrap") .length ){
                $("#brandSuggestions")
                .hide();
            }
        });
        
        const perPage=24;
        let currentPage=1;

        function renderBrandPage(page){

            currentPage=page;

            let cards=$(".brand-item");

            cards.hide();

            let start=(page-1)*perPage;
            let end=start+perPage;
            
            cards.slice(start,end).show();
            $(".brand-page-btn") .removeClass("active");
            $('.brand-page-btn[data-page="'+page+'"]' ).addClass("active");
        }

        renderBrandPage(1);

        $(document).on(
            "click",
            ".brand-page-btn",
            function(){
                renderBrandPage( parseInt( $(this).data("page") ) );
            }
        );

        $("#brandPrev").click(function(){

            if(currentPage>1){
                renderBrandPage(currentPage-1 );
            }
        });

        $("#brandNext").click(function(){

            if(currentPage< <?= $totalPages; ?> ){
                renderBrandPage(currentPage+1 );
            }
        });

    </script>

</section>


<?= $this->endSection(); ?>