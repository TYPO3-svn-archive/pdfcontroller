plugin.tx_pdfcontroller {
    // empty statement for proper comments only
  settings {
  }
    // master_templates
  settings =
  settings {
      // empty statement for proper comments only
    master_templates {
    }
      // pdf
    master_templates =
    master_templates {
        // empty statement for proper comments only
      pdf {
      }
        // config, typeNum, CONTENT
      pdf = PAGE
      pdf {
        config {
          absRefPrefix          = {$plugin.pdfcontroller.url}
          admPanel              = 0
          disableAllHeaderCode  = {$plugin.pdfcontroller.config.disableAllHeaderCode}
          doctype               = xhtml_strict
          metaCharset           = UTF-8
          xhtml_cleaning        = 0
        }
        typeNum = {$plugin.pdfcontroller.pages.pdf.typeNum}
        10 = COA
        10 {
          10 = TEXT
          10 {
            if {
              isFalse = {$plugin.pdfcontroller.pages.userinterface.pid}
            }
            value = Please configure CONSTANT EDITOR > category [PDF CONTROLLER - PAGES USER INTERFACE]
            lang {
              de = Bitte kümmere Dich um: CONSTANT EDITOR > category [PDF CONTROLLER - PAGES USER INTERFACE]
              en = Please take care of: CONSTANT EDITOR > category [PDF CONTROLLER - PAGES USER INTERFACE]
            }
            wrap  = <h1>|</h1>
          }
          20 = COA
          20 {
            if {
              isTrue = {$plugin.pdfcontroller.pages.userinterface.pid}
            }
              // header, content, footer
            10 < styles.content.get
            10 {
              select {
                pidInList = {$plugin.pdfcontroller.pages.userinterface.pid}
              }
            }
          }
        }
      }
    }
  }
}