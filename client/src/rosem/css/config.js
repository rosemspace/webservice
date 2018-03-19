export default {
  breakpoints: {
    "--small-phone": "(min-width: 336px)",
    "--phone": "(min-width: 544px)",
    "--tablet": "(min-width: 768px)",
    "--desktop": "(min-width: 992px)",
    "--large-desktop": "(min-width: 1200px)"
  },
  selectors: {
    ":--heading": "h1, h2, h3, h4, h5, h6",
    ":--list": "ul, ol",
    ":--preformatted": "pre",
    ":--block":
      "p, :--heading, :--list, :--preformatted, dl, div, noscript, blockquote, form, hr, table, fieldset, address",
    ":--fontstyle": "tt, i, b, big, small",
    ":--phrase": "em, strong, dfn, code, samp, kbd, var, cite, abbr, acronym",
    ":--special": "a, img, object, br, script, map, q, sub, sup, span, bdo",
    ":--formctrl": "input, select, textarea, label, button",
    ":--form-button":
      "input:matches([type=button],[type=reset],[type=submit]), button",
    ":--button": ":--form-button, [role=button]",
    ":--inline": ":--fontstyle, :--phrase, :--special, :--formctrl",
    ":--flow": ":--block, :--inline",
    ":--text-input":
      "input:not([type=" +
      [
        "button",
        "checkbox",
        "color",
        "file",
        "hidden",
        "image",
        "radio",
        "range",
        "reset",
        "submit"
      ].join("],[type=") +
      "]), textarea, [contenteditable=true], [role=textbox]"
  },
  variables: {
    "primary-color": "blue"
  }
};
