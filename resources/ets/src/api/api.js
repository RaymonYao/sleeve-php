import service from '../utils/request'

export const login = (code,type) => {
  return service({
    url: '/api/login/' + code,
    method: 'get',
    params: {
      env: type,
    }
  });
}

export const getStafflist = () => {
  return service({
    url: '/api/sys/user',
    method: 'post',
  });
}

export const getDepartment = (data) => {
  return service({
    url: '/api/sys/dept',
    method: 'post',
    data
  });
}

export const getTicketlist = (data) => {
  return service({
    url: '/api/ticket',
    method: 'post',
    data
  });
}

export const createTicket = (data) => {
  return service({
    url: '/api/ticket/save',
    method: 'post',
    data
  });
}

export const getTicketDetail = (data) => {
  return service({
    url: '/api/ticket/detail',
    method: 'post',
    data
  });
}

export const getReply = (data) => {
  return service({
    url: '/api/post',
    method: 'post',
    data
  });
}

export const postReply = (data) => {
  return service({
    url: '/api/post/reply',
    method: 'post',
    data
  });
}

export const getOplog = (data) => {
  return service({
    url: '/api/ticket/getOpLogList',
    method: 'post',
    data
  });
}

export const postAssign = (data) => {
  return service({
    url: '/api/ticket/assign',
    method: 'post',
    data
  });
}

export const cancelTicket = (data) => {
  return service({
    url: '/api/ticket/cancel',
    method: 'post',
    data
  });
}

export const completeTicket = (data) => {
  return service({
    url: '/api/ticket/complete',
    method: 'post',
    data
  });
}

export const upload = (data) => {
  return service({
    url: '/api/sys/upload',
    method: 'post',
    data
  });
}

export const urgeTicket = (data) => {
  return service({
    url: '/api/ticket/urge',
    method: 'post',
    data
  });
}