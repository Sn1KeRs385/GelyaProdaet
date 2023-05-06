enum CollectionName {
  IMAGE = 'image',
  VIDEO = 'video',
  PREVIEW = 'preview',
  FILE = 'file',
  LIST_OPTION_LOGO = 'list_option_logo',
}

export const isVideoCollection = (collectionName: CollectionName) => {
  return [CollectionName.VIDEO].includes(collectionName)
}
export const isImageCollection = (collectionName: CollectionName) => {
  return [CollectionName.IMAGE, CollectionName.LIST_OPTION_LOGO].includes(collectionName)
}

export default CollectionName
