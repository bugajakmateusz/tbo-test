export interface MachineFromApi {
    id: string
    type: string
    attributes: {
        location: string
        positionsNumber: string
        positionsCapacity: string
    }
}
