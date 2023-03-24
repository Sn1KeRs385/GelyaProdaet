import FileStatus from 'file-uploader/enums/file-status'

export default {
  fileStatuses: {
    [FileStatus.FINISHED]: 'Обработка завершена',
    [FileStatus.CREATED]: 'Загружен но не обработан',
    [FileStatus.CONVERTING]: 'Находится в обработке',
    [FileStatus.DELETED]: 'Удален',
    [FileStatus.ERROR]: 'Ошибка обработки',
    [FileStatus.UPLOADING]: 'Загружается',
    [FileStatus.WAITING_QUEUE]: 'В очереди на обработку',
  },
}
