export interface WarehouseSnackFromApi {
    id: string
    type: string
    attributes: {
        name: string,
        quantity: number
    }
}
