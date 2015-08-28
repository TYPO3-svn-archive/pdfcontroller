  // for proper comments only
start {
}
  // gridelements
start =
start {
    // for proper comments only
  gridelements {
  }
    // 1 column
  gridelements =
  gridelements {
    1 < lib.gridelements.defaultGridSetup
      // for proper comments only
    1 {
    }
      // 1 column: wrap, columns
    1 =
    1 {
      wrap {
        stdWrap {
          cObject = COA
          cObject {
              // <div ...
            10 = TEXT
            10 {
              value = <div class="row
            }
              // class
            20 = TEXT
            20 {
              field       = flexform_class
              noTrimWrap  = | ||
              required    = 1
            }
              // ... /div>
            30 = TEXT
            30 {
              value = ">|</div>
            }
          }
        }
      }
      columns {
        default {
          wrap {
            stdWrap {
              cObject = COA
              cObject {
                  // class
                10 = TEXT
                10 {
                  field       = flexform_class
                  noTrimWrap  = | ||
                  required    = 1
                }
              }
            }
            noTrimWrap = |<div class="columns|">|
          }
          wrap2 = |</div>
        }
      }
    }
  }
}

tt_content.gridelements_pi1.20.10.setup.1 < start.gridelements.1