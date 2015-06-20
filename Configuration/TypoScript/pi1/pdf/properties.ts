plugin.tx_pdfcontroller_pi1 {
    // empty statement for proper comments only
  pdf {
  }
    // properties
  pdf =
  pdf {
      // empty statement for proper comments only
    properties {
    }
      // documentAuthor, documentKeywords, documentSubject, documentTitle
    properties =
    properties {
        // page : author
      documentAuthor = CONTENT
      documentAuthor {
        table = pages
        select {
          where = 1 OR (uid = {page:uid} AND pid >= 0)
          where {
            insertData = 1
          }
          max = 1
        }
        renderObj = TEXT
        renderObj {
          field = author
        }
      }
        // page : keywords
      documentKeywords < .documentAuthor
      documentKeywords {
        renderObj {
          field = keywords
        }
      }
        // page : description
      documentSubject < .documentAuthor
      documentSubject {
        renderObj {
          field = description
        }
      }
        // page : title
      documentTitle < .documentAuthor
      documentTitle {
        renderObj {
          field = title
        }
      }
    }
  }
}