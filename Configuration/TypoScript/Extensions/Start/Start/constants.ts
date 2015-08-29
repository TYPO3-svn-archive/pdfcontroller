plugin.tx_pdfcontroller {
  extensions {
    start {
      # cat=PDF Controller - EXT:start - templates//100; type=string; label= Layout:Path to the fluid layouts. With ending slash!
      fluid.path.layouts    = EXT:pdfcontroller/Resources/Private/Extensions/Start/Templates/Layouts/
      # cat=PDF Controller - EXT:start - templates//101; type=string; label= Partials:Path to the fluid partials. With ending slash!
      fluid.path.partials   = EXT:pdfcontroller/Resources/Private/Extensions/Start/Templates/Partials/
      # cat=PDF Controller - EXT:start - templates//102; type=string; label= Templates:Path to the fluid templates. With ending slash!
      fluid.path.templates  = EXT:pdfcontroller/Resources/Private/Extensions/Start/Templates/
    }
  }
}
