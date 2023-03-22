export const loadingButton = async (event: Event, callback: () => void) => {
  console.log(event)
  await callback()
}
