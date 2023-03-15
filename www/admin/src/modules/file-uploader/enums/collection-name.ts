enum CollectionName {
  IMAGE = 'image',
  VIDEO = 'video',
  PREVIEW = 'preview',
}

export const isVideoCollection = (collectionName: CollectionName) => {
  return [CollectionName.VIDEO].includes(collectionName)
}
export const isImageCollection = (collectionName: CollectionName) => {
  return [CollectionName.IMAGE].includes(collectionName)
}

export default CollectionName
