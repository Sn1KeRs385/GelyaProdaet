import { api, axios } from 'src/boot/axios'
import CollectionName from 'file-uploader/enums/collection-name'
import FileUploadInfoInterface from 'file-uploader/interfaces/file-upload-info-interface'
import { t } from 'src/boot/i18n'

const uploaderUrl = `${process.env.BACKEND_URL}/uploader-module`

export const uploadFile = async (uploadFile: FileUploadInfoInterface) => {
  try {
    await registerFile(uploadFile, uploadFile.collectionName)
    const blob = uploadFile.file
    const portion = uploadFile.bytesPerRequest
    let from = 0
    let to = 0

    while (blob.size > from) {
      try {
        await uploadPortion(uploadFile, from, to, blob)
        from += portion
        to = from + portion
      } catch (error) {
        const errors = []
        if (axios.isAxiosError(error)) {
          if (error?.response?.data?.[0]?.message) {
            errors.push(error.response.data[0].message)
          }
        } else {
          errors.push(t('axios.undefinedError'))
        }

        uploadFile.errors = errors
        return
      }
    }
    await finishUpload(uploadFile)
  } catch {
    uploadFile.errors = [t('axios.undefinedError')]
    return
  }
}

async function registerFile(uploadFile: FileUploadInfoInterface, collectionName: CollectionName) {
  const { data } = await api.post(`${uploaderUrl}/register`, {
    name: uploadFile.file.name,
    collection_name: collectionName,
  })

  uploadFile.id = data.id
}

function uploadPortion(uploadFile: FileUploadInfoInterface, from: number, to: number, blob: Blob) {
  return new Promise((res, rej) => {
    const reader = new FileReader()

    reader.onloadend = async (evt) => {
      try {
        if (evt.target?.readyState === FileReader.DONE) {
          await uploadBlob(uploadFile, from, convertToBinary(evt.target.result))
          const percentComplete = Math.round(to / (blob.size / 100))
          uploadFile.progress = percentComplete >= 100 ? 100 : percentComplete
          // eslint-disable-next-line @typescript-eslint/ban-ts-comment
          // @ts-ignore
          res()
        }
      } catch (error) {
        rej(error)
      }
    }

    const slice = getFileSlice(uploadFile, from)
    reader.readAsBinaryString(slice)
  })
}

async function uploadBlob(
  uploadFile: FileUploadInfoInterface,
  from: number,
  blobPart: ArrayBuffer
) {
  await api({
    method: 'PATCH',
    url: `${uploaderUrl}/upload`,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/x-binary; charset=x-user-defined',
      'File-Id': uploadFile.id,
      'Portion-From': from,
      'Portion-Size': uploadFile.bytesPerRequest,
    },
    data: new Blob([blobPart], {
      type: 'application/x-binary; charset=x-user-defined',
    }),
  })
}

async function finishUpload(uploadFile: FileUploadInfoInterface) {
  await api({
    method: 'PATCH',
    url: `${uploaderUrl}/finish`,
    headers: {
      'File-Id': uploadFile.id,
    },
  })

  uploadFile.progress = 100
  uploadFile.isFinishedUpload = true
}

function getFileSlice(uploadFile: FileUploadInfoInterface, from: number) {
  const portion = uploadFile.bytesPerRequest

  if (uploadFile.file.slice) {
    return uploadFile.file.slice(from, from + portion)
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
  } else if (uploadFile.file.webkitSlice) {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    return uploadFile.file.webkitSlice(from, from + portion)
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
  } else if (uploadFile.file.mozSlice) {
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    return uploadFile.file.mozSlice(from, from + portion)
  }
}

const convertToBinary = (stringData: string | ArrayBuffer | null) => {
  const ords = Array.prototype.map.call(stringData, (x) => {
    return x.charCodeAt(0) & 0xff
  })
  // eslint-disable-next-line @typescript-eslint/ban-ts-comment
  // @ts-ignore
  const ui8a = new Uint8Array(ords)
  return ui8a.buffer
}

export default { uploadFile }
