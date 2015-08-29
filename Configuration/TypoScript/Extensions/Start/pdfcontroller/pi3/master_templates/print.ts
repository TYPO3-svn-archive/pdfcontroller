plugin.tx_pdfcontroller {
    // empty statement for proper comments only
  settings {
  }
    // extensions
  settings =
  settings {
      // empty statement for proper comments only
    extensions {
    }
      // start
    extensions =
    extensions {
        // empty statement for proper comments only
      start {
      }
        // master_templates
      start =
      start {
          // empty statement for proper comments only
        master_templates {
        }
          // print
        master_templates =
        master_templates {
            // empty statement for proper comments only
          print {
          }
            // config, typeNum, CONTENT
          print = PAGE
          print {
            config {
              absRefPrefix          = {$plugin.pdfcontroller.url}
              admPanel              = 0
              disableAllHeaderCode  = 1
              doctype               = xhtml_strict
              metaCharset           = UTF-8
              xhtml_cleaning        = 0
            }
            bodyTagCObject = TEXT
            bodyTagCObject {
              insertData = 1
              value = <body id="bodyId-{TSFE:id}">
            }
            typeNum = {$plugin.pdfcontroller.pages.print.typeNum}

              // start FLUIDTEMPLATES
            63148 = CASE
            63148 {
              key {
                data = levelfield:-2,backend_layout_next_level,slide
                override {
                  field = backend_layout
                }
              }
              default = TEXT
              default {
                data = levelfield:-2,backend_layout_next_level,slide
                override {
                  field = backend_layout
                }
                noTrimWrap {
                  stdWrap {
                    cObject = TEXT
                    cObject {
                      value = |<div style="background:white;border:red solid 1em;color:red;font-weight:bold;margin:2em;padding:1em;text-align:center;">The current backend layout "|" can't handled. Please choose a proper backend layout. This is a prompt of the Starter Kit Responsive (start). </div>|
                      lang {
                        de = |<div style="background:white;border:red solid 1em;color:red;font-weight:bold;margin:2em;padding:1em;text-align:center;">Das aktuelles Backend Layout "|" kann nicht verarbeitet werden. Bitte w&auml;hle ein g&uuml;ltiges Backend Layout aus. Das ist eine Meldung des Starter Kit Responsive (start). </div>|
                      }
                    }
                  }
                }
              }
                // One column: content
              start__bronze_01 = FLUIDTEMPLATE
              start__bronze_01 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}bronze_01.html
                layoutRootPath  = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.layouts}
                partialRootPaths {
                  10 = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.partials}
                }
                variables {
                }
                settings {
                  classColMainContentMain =
                }
                stdWrap {
                  wrap (
                    <!--[if lt IE 9]>
                      <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
                    <![endif]-->
                    |
          )
                }
              }
                // Two columns: content | right
              start__bronze_02 < .start__bronze_01
              start__bronze_02 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}bronze_02.html
                settings {
                  classColMainContentMain   >
                  classColMainContentMain   = small-12 medium-8
                  classColMainContentRight  = show-for-medium-up medium-4
                }
              }
                // Two columns: left | content
              start__bronze_03 < .start__bronze_01
              start__bronze_03 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}bronze_03.html
                settings {
                  classColMainContentMain >
                  classColMainContentLeft = show-for-medium-up medium-4
                  classColMainContentMain = small-12 medium-8
                }
              }
                // Three columns: left | content | right
              start__bronze_04 < .start__bronze_01
              start__bronze_04 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}bronze_04.html
                settings {
                  classColMainContentMain   >
                  classColMainContentLeft   = show-for-medium-up medium-3
                  classColMainContentMain   = small-12 medium-6
                  classColMainContentRight  = show-for-medium-up medium-3
                }
              }
                // Four columns
              start__default < .start__bronze_01
              start__default {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}default.html
              }
              start__gold_01 < .start__bronze_01
              start__gold_01 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}gold_01.html
              }
              start__gold_02 < .start__bronze_02
              start__gold_02 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}gold_02.html
              }
              start__gold_03 < .start__bronze_03
              start__gold_03 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}gold_03.html
              }
              start__gold_04 < .start__bronze_04
              start__gold_04 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}gold_04.html
              }
              // #i0027, 150425, dwildt, 4+
              start__gold_01 >
              start__gold_02 >
              start__gold_03 >
              start__gold_04 >
                // Two rows: header (left | center | right) | content
              start__silver_01 < .start__bronze_01
              start__silver_01 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}silver_01.html
                settings >
                settings {
                  classColMainContentTopLeft      = small-12 medium-4 large-4
                  classColMainContentTopCenter    = small-12 medium-4 large-4
                  classColMainContentTopRight     = small-12 medium-4 large-4
                  classColMainContentMain         = small-12
                }
              }
                // Three rows: header (left | center | right) | content | bottom (left | center | right)
              start__silver_02 < .start__bronze_02
              start__silver_02 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}silver_02.html
                settings >
                settings {
                  classColMainContentTopLeft      = small-12 medium-4 large-4
                  classColMainContentTopCenter    = small-12 medium-4 large-4
                  classColMainContentTopRight     = small-12 medium-4 large-4
                  classColMainContentMain         = small-12
                  classColMainContentBottomLeft   = small-12 medium-4 large-4
                  classColMainContentBottomCenter = small-12 medium-4 large-4
                  classColMainContentBottomRight  = small-12 medium-4 large-4
                }
              }
                // Two rows: content | bottom (left | center | right)
              start__silver_03 < .start__bronze_03
              start__silver_03 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}silver_03.html
                settings >
                settings {
                  settings.styleTable       = width:600px;
                  settings.styleTableTr0Td0 = background-color:silver;
                  settings.styleTableTr1Td0 = width:33%;
                  settings.styleTableTr1Td1 = width:34%;
                  settings.styleTableTr1Td2 = width:33%;
                }
              }
                // One column
              start__simple_01 < .start__bronze_01
              start__simple_01 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}simple_01.html
              }
                // Two columns: content | right
              start__simple_02 < .start__bronze_02
              start__simple_02 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}simple_02.html
              }
                // Two columns: left | content
              start__simple_03 < .start__bronze_03
              start__simple_03 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}simple_03.html
              }
                // Three columns: left | content | right
              start__simple_04 < .start__bronze_04
              start__simple_04 {
                file = {$plugin.tx_pdfcontroller.extensions.start.fluid.path.templates}simple_04.html
              }
            }
          }
        }
      }
    }
  }
}