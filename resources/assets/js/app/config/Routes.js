'use strict'

let routes = []

function mergeRoutes(route_metas_array) {
    route_metas_array.map(route_meta_array => {
        route_meta_array.map(route_meta => {
            routes.push(route_meta)
        })
    })
}
import RoutesCommon from '@/config/routes_meta/RoutesCommon'
import RoutesSettings from '@/config/routes_meta/RoutesSettings'

mergeRoutes([RoutesCommon, RoutesSettings])

export default routes
