<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>
        <?= $page_title ?? 'K-Pop Merch'; ?>
    </title>

    <link 
        rel="icon"
        type="image/png"
        href="<?= base_url('assets/images/logo.png'); ?>"
    >


    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet"
          href="<?= base_url('css/theme.css'); ?>">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>

<body>

    <!-- HEADER -->

    <?= view('layouts/header'); ?>


    <!-- PAGE CONTENT -->

    <?= $this->renderSection('content'); ?>


    <!-- FOOTER -->

    <?= view('layouts/footer'); ?>
    
    <!-- for alerts -->
    <div id="themeAlert" class="theme-alert">

        <div class="theme-alert-overlay"></div>

        <div class="theme-alert-box">

            <div class="theme-alert-icon">
                <i id="themeAlertIcon"></i>
            </div>

            <h3 id="themeAlertTitle"></h3>

            <p id="themeAlertMessage"></p>

            <div
                id="themeAlertButtons"
                class="theme-alert-buttons">

            </div>

        </div>

    </div>
    
    <!-- custom loader -->
    <div id="siteLoader">
        <div class="site-loader-content">
            <img
                src="<?= base_url('assets/loader.gif'); ?>"
                alt="Loading"
            >
        </div>
    </div>

</body>

<script>

    function closeAlert()
    {
        $("#themeAlert").fadeOut(200);
    }

    function showAlert(type, title, message)
    {
        let icon = 'fa-circle-info';

        if(type == 'success')
        {
            icon = 'fa-circle-check';
        }

        if(type == 'error')
        {
            icon = 'fa-circle-xmark';
        }

        if(type == 'warning')
        {
            icon = 'fa-triangle-exclamation';
        }

        $("#themeAlertIcon")
            .attr("class", "fa-solid " + icon);

        $(".theme-alert-icon")
            .removeClass("success error warning info")
            .addClass(type);

        $("#themeAlertTitle").html(title);

        $("#themeAlertMessage").html(message);

        $("#themeAlertButtons").html(
            '<button ' +
            'class="theme-alert-btn theme-alert-confirm" ' +
            'onclick="closeAlert()">' +
            'OK' +
            '</button>'
        );

        $("#themeAlert").fadeIn(200);
    }

    function showConfirm(options)
    {
        $("#themeAlertIcon")
        .attr("class", "fa-solid fa-circle-exclamation"
        );

        $(".theme-alert-icon")
        .removeClass(
            "success error warning info"
        )
        .addClass("warning");

        $("#themeAlertTitle") .html(options.title);

        $("#themeAlertMessage") .html(options.message);

        $("#themeAlertButtons").html(
            '<button '+
            'class="theme-alert-btn theme-alert-cancel" '+
            'onclick="closeAlert()">'+
            'Cancel'+
            '</button>'+
            '<button '+
            'id="themeConfirmBtn" '+
            'class="theme-alert-btn theme-alert-confirm">'+
            (options.confirmText || 'Confirm')+
            '</button>'
        );

        $("#themeAlert") .fadeIn(200);

        $("#themeConfirmBtn")
        .off("click") .on( "click",function(){

                closeAlert();
                if(options.callback)
                {
                    options.callback();
                }
            }
        );
    }
    
    function showLoader()
    {
        $("#siteLoader").fadeIn(100);
    }

    function hideLoader()
    {
        $("#siteLoader").fadeOut(100);
    }
    
    function openBand(brandId, brandName)
    {
        if(!brandId)
        {
            return;
        }

        $("#siteLoader").fadeIn(200);

        let form = $('<form>', {
            method: 'POST',
            action: '<?= base_url("getProductsByBand"); ?>'
        });

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'brand_id',
                value: brandId
            })
        );

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'brand_name',
                value: brandName
            })
        );

        $('body').append(form);

        form.submit();
    }
    
     //to open per category page
    function openCategory(catId, catName)
    {
        if(!catId)
        {
            return;
        }

        $("#siteLoader").fadeIn(200);

        let form = $('<form>', {
            method: 'POST',
            action: '<?= base_url("getProductsByCategory"); ?>'
        });

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'cat_id',
                value: catId
            })
        );

        form.append(
            $('<input>', {
                type: 'hidden',
                name: 'category_name',
                value: catName
            })
        );

        $('body').append(form);

        form.submit();
    }
    
    //for input alert
    function showInputAlert(options)
    {
        $("#themeAlertIcon")
            .attr("class", "fa-solid " + (options.icon || "fa-envelope"));

        $(".theme-alert-icon")
            .removeClass("success error warning info")
            .addClass(options.type || "info");

        $("#themeAlertTitle").html(options.title || "");

        $("#themeAlertMessage").html(options.message || "");

        $("#themeAlertButtons").html(
            '<div class="theme-alert-input-wrap">' +
                '<input ' +
                    'type="' + (options.inputType || "text") + '" ' +
                    'id="themeAlertInput" ' +
                    'class="theme-alert-input" ' +
                    'placeholder="' + (options.placeholder || "") + '">' +
            '</div>' +

            '<button ' +
                'id="themeInputAlertBtn" ' +
                'class="theme-alert-btn theme-alert-confirm">' +
                (options.buttonText || "Submit") +
            '</button>'
        );

        $("#themeAlert").fadeIn(200);

        $("#themeAlertInput").focus();

        $("#themeInputAlertBtn")
            .off("click")
            .on("click", function(){

                let value = $("#themeAlertInput").val().trim();

                if(options.callback)
                {
                    options.callback(value);
                }
            });

        $("#themeAlertInput")
            .off("keypress")
            .on("keypress", function(e){

                if(e.which == 13)
                {
                    $("#themeInputAlertBtn").click();
                }
            });
    }

</script>

</html>