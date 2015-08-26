plugin.tx_pdfcontroller {
    // empty statement for proper comments only
  settings {
  }
    // pdf
  settings =
  settings {
      // empty statement for proper comments only
    pdf {
    }
      // pageproperties
    pdf =
    pdf {
        // empty statement for proper comments only
      pageproperties {
      }
        // documentAuthor, documentKeywords, documentSubject, documentTitle
      pageproperties =
      pageproperties {
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
}