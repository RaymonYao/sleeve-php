module.exports = {
    outputDir: '../../public',
    devServer: {
        port: 8080,
        proxy: {
            "/dv": {
                target: 'https://ets-test.ihercules.cn/',
                ws: true,
                changeOrigin: true,
                pathRewrite: {
                    '^/dv': ''
                }
            }
        },
    },
    chainWebpack: config => {
        config
            .plugin('html')
            .tap(args => {
                args[0].title = '蜂鸟工单'
                return args
            })
    }
}
