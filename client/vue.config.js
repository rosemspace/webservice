const path = require("path"),
  ExtractTextPlugin = require("extract-text-webpack-plugin"),
  HtmlWebpackPlugin = require("html-webpack-plugin");

module.exports = {
  // lintOnSave: true,
  configureWebpack: config => {
    // console.log(config);
    // console.log(config.module.rules.find(value => value.test.test('.vue')).use[0].options.loaders);
    console.log(config.plugins);
    const extractCSS = config.plugins.find(
      plugin => plugin instanceof ExtractTextPlugin
    );
    const extractHTML = config.plugins.find(
      plugin => plugin instanceof HtmlWebpackPlugin
    );
    // extractHTML.options.template = "/home/smile/Documents/ma1oy/work/projects/rosem/webservice/app/Rosem/Kernel/resources/views/layouts/base.phtml";

    return {
      entry: {
        // site: './src/rosem/site',
        admin: "./src/rosem/admin",
        rosem: "rosem.css"
      },
      resolve: {
        alias: {
          "rosem.css": path.resolve(__dirname, "src/rosem/css/style.pcss"),
          "rosem-ui": path.resolve(__dirname, "src/rosem/ui"),

          "@rosem": path.resolve(__dirname, "src/rosem")
        }
      },
      // output: {
      //     path: __dirname + "/cool-build"
      // },
      module: {
        rules: [
          {
            test: /\.pcss$/,
            use: [
              "style-loader",
              {
                loader: "css-loader",
                options: {
                  sourceMap: true
                }
              },
              {
                loader: "postcss-loader",
                options: {
                  sourceMap: true
                }
              }
            ]
          }
          // {
          //   test: /\.css$/,
          //   use: extractCSS.extract({
          //     fallback: "style-loader",
          //     use: "css-loader"
          //   })
          // },
          // {
          //   test: /\.pcss$/,
          //   use: extractCSS.extract({
          //     fallback: "style-loader",
          //     use: [
          //       {
          //         loader: "css-loader",
          //         options: {
          //           sourceMap: true
          //         }
          //       },
          //       {
          //         loader: "postcss-loader",
          //         options: {
          //           sourceMap: true
          //         }
          //       }
          //     ]
          //   })
          // }
        ]
      }
    };
  }
};
