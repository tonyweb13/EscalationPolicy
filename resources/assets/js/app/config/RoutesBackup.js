'use strict'

let routes = []

function mergeRoutes(route_metas_array) {
    route_metas_array.map(route_meta_array => {
        route_meta_array.map(route_meta => {
            routes.push(route_meta)
        })
    })
}
import RoutesAdmin from '@/config/routes_meta/RoutesAdmin'
import RoutesEmployee from '@/config/routes_meta/RoutesEmployee'
import RoutesSettings from '@/config/routes_meta/RoutesSettings'

mergeRoutes([RoutesEmployee, RoutesSettings, RoutesAdmin])

export default routes
