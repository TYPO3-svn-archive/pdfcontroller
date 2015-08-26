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
      // print
    master_templates =
    master_templates {
        // empty statement for proper comments only
      print {
      }
        // contentRight
      print = COA
      print {
        contentRight < plugin.tx_pdfcontroller_pi3.master_templates.print.content
        contentRight {
            // header, content, footer
          10 {
            20 >
              // content
            20 = COA
            20 {
              10 < styles.content.get
              10 {
                wrap = <td style="width=66%;" valign="top">|</td>
              }
              20 < styles.content.getRight
              20 {
                slide = -1
                wrap = <td style="padding-left:1em;width=34%;" valign="top">|</td>
              }
              wrap = <table id="content" border="0" width="100%"><tr>|</tr></table>
            }
          }
        }
      }
    }
  }
}